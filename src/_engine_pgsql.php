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

    public function checkTableExists(Behavior $behavior)
    {
        $oSql = \PMVC\plug('orm')->sql();
        $sql = <<<EOF
  SELECT EXISTS (
    SELECT FROM 
        information_schema.tables 
    WHERE 
        table_schema LIKE 'public' AND 
        table_type LIKE 'BASE TABLE' AND
        table_name = {$oSql->getBindName($behavior->params)} 
  );
EOF;
        $result = $oSql->set($sql)->commit('one');
        return !empty($result['exists']);
    }
}
