<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\DSN';

use PDO;
use PMVC\PlugIn\orm\Behaviors\BuildDsn;

class DSN
{
    public function __invoke()
    {
        return $this;
    }

    public function getSupportEngine()
    {
        $arr = [MYSQL, PGSQL, SQLITE];
        $supportPdo = PDO::getAvailableDrivers();
        return array_intersect($arr, $supportPdo);
    }

    public function buildDsn()
    {
        $result =  $this->caller->compile([
            new BuildDsn($this)
        ]);
        return \PMVC\get($result, 0);
    }
}

