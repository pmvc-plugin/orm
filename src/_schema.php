<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Schema';

Class Schema {
    public function __invoke()
    {
        return $this;
    }

    public function fromModels($modelFiles)
    {
        $schemaMap = [];
        foreach ($modelFiles as $mFile) {
            extract(\PMVC\assign(['table', 'all'], \PMVC\passByRef($this->caller->parse_model($mFile))));
            foreach ($all as $col) {
                $table->addColumn($col);
            }
            $schemaMap[$table['TABLE_NAME']] = $table;
        }
        return $schemaMap;
    }

    public function fromMigrations()
    {

    }

    public function fromDb()
    {

    }

    public function buildMigration($payload, $migrationFolder)
    {
        $pOrm = \PMVC\plug('orm');
        $migrationFolder = \PMVC\realpath($migrationFolder);
        if (!empty($migrationFolder)) {
            $file = $migrationFolder. '/0001_initial.php'; 
            $content = $pOrm->useTpl('migration', $payload); 
            file_put_contents($file, $content);
        }

    }

    public function diffFromModelToMigration($modelFiles, $migrationFolder = '')
    {
        $modelSchema = $this->fromModels($modelFiles);
        $pOrm = \PMVC\plug('orm');
        foreach ($modelSchema as $model) {
            $upCommand = $pOrm->build_migration()->buildCreateModel($model->toArray());
            $this->buildMigration([
              'MIGRATION_NAME' => '0001',
              'MIGRATION_DEP' => '',
              'MIGRATION_UP' => $upCommand,
              'MIGRATION_DOWN'=> '',
            ], $migrationFolder);
        }
    }

    public function diffFromDbToMigration()
    {
    }
}
