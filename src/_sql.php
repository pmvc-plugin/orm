<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetSql';

use PMVC\PlugIn\orm\BindTrait;
use PMVC\HashMap;

class GetSql
{
    public function __invoke($sql = null)
    {
        return new RawSql($sql);
    }
}

class RawSql extends HashMap
{
    use BindTrait;

    public function __construct($sql = null)
    {
        if (!is_null($sql)) {
          $this['sql'] = $sql;
        }
    }

    public function __toString()
    {
        return (string)$this['sql'];
    }

    public function set($sql)
    {
        $this['sql'] = $sql;
        return $this;
    }

    public function commit($type = null)
    {
        $pdo = \PMVC\plug('orm')->pdo();
        $bindData = $this->getBindData();
        $sql = $this->__toString();
        switch ($type) {
          case "exec":
            return $pdo->exec($sql, $bindData);
          case "one":
            return $pdo->getOne($sql, $bindData);
          case "all":
          default:
            return $pdo->getAll($sql, $bindData);
        }
    }
}
