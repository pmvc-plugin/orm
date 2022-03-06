<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\ProcessMigraton';

class ProcessMigraton
{
    public function __invoke($files, $dao)
    {
        foreach ($files as $f) {
            $r = \PMVC\l($f, _INIT_CONFIG);
            $class = \PMVC\getExportClass($r);
            $obj = new $class();
            $obj->operations($dao);
        }
    }
}
