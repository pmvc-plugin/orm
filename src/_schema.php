<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Schema';

class Schema
{
    public function __invoke()
    {
        return $this;
    }

    public function fromModels($modelFiles)
    {
        $schemaMap = new Tables();
        $modelFiles = $this->caller->get_all_files($modelFiles);
        foreach ($modelFiles as $mFile) {
            extract(
                \PMVC\assign(
                    ['table', 'allColumns'],
                    \PMVC\passByRef($this->caller->parse_model($mFile))
                )
            );
            foreach ($allColumns as $col) {
                $table->addColumn($col);
            }
            $schemaMap[$table['TABLE_NAME']] = $table;
        }
        return $schemaMap->toArray();
    }

    public function fromMigrations($migrationFiles)
    {
        $migrationFiles = $this->caller->get_all_files(
            $migrationFiles,
            '[0-9]*.php'
        );
        $oDao = $this->caller->dao()->getDao('structure');
        $this->caller->process_migration($migrationFiles, $oDao);
        return $oDao->toArray();
    }

    public function fromDb()
    {
    }

    public function writeMigration($payload, $migrationFolder)
    {
        $pOrm = \PMVC\plug('orm');
        $migrationFolder = \PMVC\realpath($migrationFolder);
        if (!empty($migrationFolder)) {
            $file = $migrationFolder . '/0001_initial.php';
            $content = $pOrm->useTpl('migration', $payload);
            file_put_contents($file, $content);
        }
    }

    public function diffFromModelToMigration($modelFiles, $migrationFolder = '')
    {
        $modelSchema = $this->fromModels($modelFiles);
        $migrationSchema = $this->fromMigrations([$migrationFolder]);
        $tableDiff = $this->caller->diff()->diffAll($modelSchema, $migrationSchema);
        $pOrm = $this->caller;
        $newTables = \PMVC\value($tableDiff, ['tables', 'diff', 'left']);
        $delTables = \PMVC\value($tableDiff, ['tables', 'diff', 'right']);
        $colDiffs = \PMVC\get($tableDiff, 'columns');
        $commands = [];
        foreach ($newTables as $tb) {
            $commands[] = $pOrm
                ->build_migration()
                ->buildCreateModel($tb);
        }
        foreach ($colDiffs as $tableName => $diffVal) {
            \PMVC\d(compact('tableName',  'diffVal'));
        }
        

        /*
        foreach ($modelSchema as $model) {
            $upCommand = $pOrm
                ->build_migration()
                ->buildCreateModel($model->toArray());
            $this->writeMigration(
                [
                    'MIGRATION_NAME' => '0001',
                    'MIGRATION_DEP' => '',
                    'MIGRATION_OP' => $upCommand,
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
