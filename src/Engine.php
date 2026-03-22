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
        if (!empty($this->transform)) {
          $behavior->transform = $this->transform;
        }
        return $behavior;
    }

    public function buildDsn(Behavior $behavior)
    {
        throw new DomainException('Can not get dsn with default Enginee.');
    }


    public function getColumnType(Behavior $behavior)
    {
        return $behavior;
    }

    public function checkTableExists(Behavior $behavior)
    {
        throw new DomainException('Can not checkTableExists with default Enginee.');
    }

    public function buildDropTable(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildAddColumn(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildDropColumn(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildAlterColumn(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildRenameColumn(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildRenameTable(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildCreateIndex(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildDropIndex(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildAddConstraint(Behavior $behavior)
    {
        return $behavior;
    }

    public function buildDropConstraint(Behavior $behavior)
    {
        return $behavior;
    }

    public function getRegexSql(string $col, string $bindKey, bool $caseInsensitive = false): string
    {
        if ($caseInsensitive) {
            return "LOWER($col) REGEXP LOWER($bindKey)";
        }
        return "$col REGEXP $bindKey";
    }

    public function getAllDsnFields()
    {
      return array_merge(
        $this->getAllDsnRequired(),
        $this->getAllDsnOptional(),
      );
    }
}
