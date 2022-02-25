<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Interfaces\Behavior;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . "\GetSqliteEngine";

class GetSqliteEngine
{
  private $_engine;
  public function __invoke()
  {
    if (is_null($this->_engine)) {
      $this->_engine = new SqliteEngine();
    }
    var_dump($this->caller->dsn()->buildDsn());
    die("hihi" . PHP_EOL);
    // $this->caller->pdo(SQLITE);
    return $this->_engine;
  }
}

class SqliteEngine extends Engine
{
  public function buildDsn(Behavior $behavior)
  {
  }

  public function getAllDsnRequired()
  {
    return ["file"];
  }

  public function getAllDsnOptional()
  {
    return ["memory"];
  }
}
