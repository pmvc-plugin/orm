<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Migration';

use PMVC\PlugIn\orm\Fields\CharField;
use PMVC\PlugIn\orm\Fields\DateTimeField;

class Migration
{
    private $_recorder;

    public function __invoke()
    {
        return $this;
    }

    public function writeMigration(
        $payload,
        $migrationFolder,
        $migrationName = null,
        $type = null,
        $migrationPrefix = 'Migration'
    ) {
        $migrationFolder = \PMVC\realpath($migrationFolder);
        if (!empty($migrationFolder)) {
            $oSN = $this->get_serial_number($migrationFolder);
            extract(
                \PMVC\assign(
                    ['nextFile', 'nextName', 'lastName'],
                    $oSN->getNextFileName($migrationName, $type)
                )
            );
            $payload['MIGRATION_NAME'] = $nextName;
            $payload['MIGRATION_PREFIX'] = $migrationPrefix;
            $payload['MIGRATION_DEP'] = $lastName;
            $content = $this->caller->useTpl('migration', $payload);
            file_put_contents($nextFile, $content);
        }
    }

    private function _processEach($files, DAO $dao)
    {
        if (empty($this->_recorder)) {
            $this->_recorder = new MigrationRecorder();
        }
        foreach ($files as $f) {
            $r = \PMVC\l($f, _INIT_CONFIG);
            $class = \PMVC\importClass($r);
            $obj = new $class();
            $obj->process($dao);
        }
    }

    public function getAllFiles($fileOrDir) {
        return $this->caller->get_all_files(
            $fileOrDir,
            '[0-9]*.php'
        );
    }

    public function process($fileOrDir, DAO $oDao = null)
    {
        $migrationFiles = $this->getAllFiles($fileOrDir);
        if (is_null($oDao)) {
            $oDao = $this->caller->dao()->getDefault();
        }
        $this->_processEach($migrationFiles, $oDao);
        return $oDao;
    }
}

class MigrationRecorder
{
    private $_tableName = 'pmvc_migrations';

    public function __construct()
    {
        $remote = \PMVC\plug('orm')->remote();
        if (!$remote->exists($this->_tableName)) {
            $table = $remote->create($this->_tableName);
            $defineds = $this->getTableDefineds();
            foreach ($defineds as $col) {
                $table->addColumn($col);
            }
            $table->commit()->process();
        }
    }

    public function getTableDefineds()
    {
        return [
            new CharField('prefix', ['MAX_LENGTH' => 255]),
            new CharField('name', ['MAX_LENGTH' => 255]),
            new DateTimeField('applied', ['default' => 'NOW']),
        ];
    }
}
