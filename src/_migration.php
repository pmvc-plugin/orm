<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Migration';

class Migration
{
    public function __invoke()
    {
        return $this;
    }

    private function _processEach($files, DAO $dao)
    {
        foreach ($files as $f) {
            $r = \PMVC\l($f, _INIT_CONFIG);
            $class = \PMVC\getExportClass($r);
            $obj = new $class();
            $obj->operations($dao);
        }
    }

    public function process($fileOrDir, DAO $oDao = null)
    {
        $migrationFiles = $this->caller->get_all_files(
            $fileOrDir,
            '[0-9]*.php'
        );
        if (is_null($oDao)) {
          $oDao = $this->caller->dao()->getDefault();
        }
        $this->_processEach($migrationFiles, $oDao);
        return $oDao;
    }
}
