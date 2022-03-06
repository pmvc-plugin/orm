<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Attrs\Table;

class StructureDAO extends DAO
{
    private $_tables = [];

    public function createModel($tableName)
    {
        if (empty($this->_tables[$tableName])) {
            $this->_tables[$tableName] = new Table($tableName, $this);
        }
        return $this->_tables[$tableName];
    }

    public function toArray()
    {
        $arr = [];
        foreach ($this->_tables as $tKey => $tVal) {
            $arr[$tKey] = $tVal->toArray();
        }
        return $arr;
    }

    public function commit($sql, array $prepare = [])
    {
        return $this;
    }

    public function process()
    {
    }
}
