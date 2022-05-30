<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Interfaces\Behavior;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetSqliteEngine';

class GetSqliteEngine
{
    private $_engine;
    public function __invoke($configs)
    {
        if (is_null($this->_engine)) {
            $this->_engine = new SqliteEngine($configs);
        }
        return $this->_engine;
    }
}

class SqliteEngine extends Engine
{
    /**
     * https://www.php.net/manual/en/ref.pdo-sqlite.connection.php
     *
     * sqlite:/opt/databases/mydb.sq3
     * To access a database on disk, the absolute path has to be appended to the DSN prefix.
     *
     * sqlite::memory:
     * To create a database in memory, :memory: has to be appended to the DSN prefix.
     *
     * sqlite:
     * If the DSN consists of the DSN prefix only, a temporary database is used, which is deleted when the connection is closed.
     *
     */
    public function buildDsn(Behavior $behavior)
    {
        $store = '';
        if (!empty($this['file'])) {
            $store = $this['file'];
        } elseif (!empty($this['memory'])) {
            $store = ':memory:';
        }
        $behavior->setDsn($store);
        return $behavior;
    }

    public function getAllDsnRequired()
    {
    }

    public function getAllDsnOptional()
    {
        return ['memory', 'file'];
    }
}
