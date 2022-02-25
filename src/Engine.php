<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use DomainException;

class Engine {
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
