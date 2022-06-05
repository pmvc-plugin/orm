<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Schema';

class Schema
{
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

    public function fromDb()
    {
    }

    public function diffFromModelToMigration($modelFiles, $migrationFolder = '')
    {
        $modelSchema = $this->fromModels($modelFiles);
        $migrationSchema = $this->fromMigrations([$migrationFolder]);
        $tableDiff = $this->caller
            ->diff()
            ->diffAll($modelSchema, $migrationSchema);
        $pOrm = $this->caller;
        $newTables = \PMVC\value($tableDiff, ['tables', 'diff', 'left']);
        $delTables = \PMVC\value($tableDiff, ['tables', 'diff', 'right']);
        $colDiffs = \PMVC\get($tableDiff, 'columns');
        $commands = [];
        foreach ($newTables as $tb) {
            $commands[] = $pOrm->build_migration()->buildCreateModel($tb);
        }
        foreach ($colDiffs as $tableName => $diffVal) {
            \PMVC\d(compact('tableName', 'diffVal'));
        }

        /*
        foreach ($modelSchema as $model) {
            $upCommand = $pOrm
                ->build_migration()
                ->buildCreateModel($model->toArray());
            $this->writeMigration(
                [
                    'MIGRATION_DEP' => '',
                    'MIGRATION_PROCESS' => $upCommand,
                ],
                $migrationFolder
            );
        }
        */
    }

    public function diffFromDbToMigration()
    {
    }
}
