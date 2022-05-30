<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\Hashmap;
use DomainException;

class Engine extends Hashmap {
    public function buildCreateTable(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildColumn(Behavior $behavior)
    {
        if ($this->transform) {
          $behavior->transform = $this->transform;
        }
        return $behavior;
    }

    public function buildDsn(Behavior $behavior)
    {
        throw new DomainException('Can not get dsn with default Enginee.');
    }

    public function checkTableExists(Behavior $behavior)
    {
        throw new DomainException('Can not checkTableExists with default Enginee.');
    }

    public function getAllDsnFields()
    {
      return array_merge(
        $this->getAllDsnRequired(),
        $this->getAllDsnOptional(),
      );
    }
}
