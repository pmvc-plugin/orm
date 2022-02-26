<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn;
use PDO;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\orm';

const SQLITE = 'sqlite';
const MYSQL = 'mysql';
const PGSQL = 'pgsql';

const DATABASES = 'databases';
const DEFAULT_KEY = 'default';
const TYPE = 'type';

class orm extends PlugIn
{
    private $_engine;

    public function init()
    {
        $this->_engine = new Engine();
    }

    public function setEngine($databaseId = DEFAULT_KEY)
    {
        $configs = \PMVC\value($this, [DATABASES, $databaseId]);
        $type = \PMVC\get($configs, TYPE);
        switch ($type) {
            case SQLITE:
                $this->_engine = $this->engine_sqlite($configs);
                break;
            case MYSQL:
                $this->_engine = $this->engine_mysql($configs);
                break;
            case PGSQL:
                $this->_engine = $this->engine_pgsql($configs);
                break;
            default:
                $this->_engine = new Engine($configs);
                break;
        }
        $dsn = $this->dsn()->buildDsn();
        $this->pdo($dsn);
    }

    public function compile(array $behaviors, $engine = null)
    {
        if (is_null($engine)) {
            $engine = $this->_engine;
        }
        $res = [];
        foreach ($behaviors as $bKey => $behavior) {
            $res[$bKey] = $behavior->accept($engine);
        }
        return $res;
    }

    public function getTpl($tplKey)
    {
        $tpl = $this->getDir() . 'src/tpl/' . $tplKey . '.tpl';
        $content = file_get_contents($tpl);
        return $content;
    }

    public function useTpl($tplKey, array $keys, $values = null)
    {
        $tplContent = $this->getTpl($tplKey);
        $res = \PMVC\tplArrayReplace($tplContent, $keys, $values);
        return $res;
    }
}
