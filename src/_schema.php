<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Schema';

class Schema
{
    public $caller;

    public function __invoke()
    {
        return $this;
    }

    public function fromOneModel($modelData)
    {
        extract(\PMVC\assign(['table', 'allColumns'], $modelData));
        foreach ($allColumns as $col) {
            $table->addColumn($col);
        }
        return $table;
    }

    public function fromModels($modelFiles)
    {
        $schemaMap = new Tables();
        $modelFiles = $this->caller->get_all_files($modelFiles);
        foreach ($modelFiles as $mFile) {
            $table = $this->fromOneModel(
                $this->caller->parse_model()->fromFile($mFile)
            );
            $schemaMap[$table['TABLE_NAME']] = $table;
        }
        return $schemaMap->toArray();
    }

    public function fromMigrations($migrationFiles)
    {
        $oDao = $this->caller->dao()->getDao('structure');
        $this->caller->migration()->process($migrationFiles, $oDao);
        return $oDao->toArray();
    }

    public function diffFromModelToMigration($modelFiles, $migrationFolder = '')
    {
        $modelSchema = $this->fromModels($modelFiles);
        $migrationSchema = $this->fromMigrations([$migrationFolder]);
        $tableDiff = $this->caller
            ->diff()
            ->diffAll($modelSchema, $migrationSchema);
        $pOrm = $this->caller;
        $builder = $pOrm->build_migration();
        $newTables = \PMVC\value($tableDiff, ['tables', 'diff', 'left']);
        $delTables = \PMVC\value($tableDiff, ['tables', 'diff', 'right']);
        $colDiffs = \PMVC\get($tableDiff, 'columns');
        $commands = [];
        $reverseCommands = [];

        // New tables → createModel (reverse: deleteModel)
        foreach ($newTables ?? [] as $tableName => $tb) {
            $commands[] = $builder->buildCreateModel($tb);
            $reverseCommands[] = $builder->buildDeleteModel($tableName);
        }

        // Column diffs for existing tables
        foreach ($colDiffs ?? [] as $tableName => $diffVal) {
            // Added columns (reverse: removeField)
            $addedCols = \PMVC\value($diffVal, ['diff', 'left']);
            if (!empty($addedCols)) {
                foreach ($addedCols as $colName => $colData) {
                    $commands[] = $builder->buildAddField($tableName, $colData);
                    $reverseCommands[] = $builder->buildRemoveField($tableName, $colName);
                }
            }

            // Removed columns (reverse: addField)
            $removedCols = \PMVC\value($diffVal, ['diff', 'right']);
            if (!empty($removedCols)) {
                foreach ($removedCols as $colName => $colData) {
                    $commands[] = $builder->buildRemoveField($tableName, $colName);
                    $reverseCommands[] = $builder->buildAddField($tableName, $colData);
                }
            }

            // Changed columns (reverse: alterField with old values)
            $changedCols = \PMVC\get($diffVal, 'change');
            if (!empty($changedCols)) {
                foreach ($changedCols as $colName => $colData) {
                    $commands[] = $builder->buildAlterField($tableName, $colData['left']);
                    $reverseCommands[] = $builder->buildAlterField($tableName, $colData['right']);
                }
            }
        }

        // Deleted tables → deleteModel (reverse: createModel)
        if (!empty($delTables)) {
            foreach ($delTables as $tableName => $tableData) {
                $commands[] = $builder->buildDeleteModel($tableName);
                $reverseCommands[] = $builder->buildCreateModel($tableData);
            }
        }

        return [
            'commands' => $commands,
            'reverse' => $reverseCommands,
        ];
    }

    public function fromDb()
    {
        $pOrm = $this->caller;
        $engine = $pOrm->behavior()->getEngine();
        if (!$engine) {
            return [];
        }
        return $engine->introspectSchema();
    }

    public function diffFromDbToMigration($migrationFolder = '')
    {
        $dbSchema = $this->fromDb();
        $migrationSchema = $this->fromMigrations([$migrationFolder]);
        return $this->caller
            ->diff()
            ->diffAll($dbSchema, $migrationSchema);
    }
}
