<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Migration';

class Migration
{
    public function __invoke($dir)
    {
        $files = glob($dir.'/[0-9]*.php');
        $oDao = $this->caller->dao()->getDefault();
        $this->caller->process_migration($files, $oDao);
    }
}
