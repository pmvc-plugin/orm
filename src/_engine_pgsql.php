<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Interfaces\Behavior;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetPgsqlEngine';

class GetPgsqlEngine
{
    private $_engine;
    public function __invoke($configs)
    {
        if (is_null($this->_engine)) {
            $this->_engine = new PgsqlEngine($configs);
        }
        return $this->_engine;
    }
}

class PgsqlEngine extends Engine
{
    /**
     * https://www.php.net/manual/en/ref.pdo-pgsql.connection.php
     */
    public function buildDsn(Behavior $behavior)
    {
        $behavior->setDsn($this);
        return $behavior->process();
    }

    public function getAllDsnRequired()
    {
        return ['host', 'dbname', 'user', 'password'];
    }

    public function getAllDsnOptional()
    {
        return ['port', 'sslmode'];
    }

    public function checkTableExists($name)
    {
    }
}
