<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\Hashmap;
use DomainException;

class Engine extends Hashmap {
    public function buildCreateTable(Behavior $behavior)
    {
        return $behavior->process();
    }

    public function buildColumn(Behavior $behavior)
    {
        return $behavior->process();
    }

    public function buildDsn(Behavior $behavior)
    {
        throw new DomainException('Can not get dsn with default Enginee.');
    }
}
