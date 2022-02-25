<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Attrs\Table;

/**
 * Migration Operations
 *
 * django:
 *  https://docs.djangoproject.com/en/4.0/ref/migration-operations/
 */
class DAO
{
    private $_history = [];
    private $_queue = [];

    public function commit($sql, array $prepare = [])
    {
        $this->_queue[] = [$sql, $prepare];
        return $this;
    }

    public function process()
    {
        \PMVC\plug('orm')->pdo()->processDao($this);
    }

    public function getQueue()
    {
        return $this->_queue;
    }

    public function getHistory()
    {
        return $this->_history;
    }

    public function createModel($tableName)
    {
        return new Table($tableName, $this);
    }

    public function deleteModel()
    {
    }

    public function renameModel()
    {
    }

    public function alterUniqueTogether()
    {
    }

    public function alterIndexTogether()
    {
    }

    public function addField()
    {
    }

    public function removeField()
    {
    }

    public function alterField()
    {
    }

    public function renameField()
    {
    }
}
