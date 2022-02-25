<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn;
use PDO;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\orm';

const SQLITE = 'sqlite';
const MYSQL = 'mysql';
const PGSQL = 'pgsql';

class orm extends PlugIn
{
    private $_engine;

    public function init()
    {
        $this->_engine = new Engine();
    }

    public function setEngine($engineName)
    {
        switch ($engineName) {
            case SQLITE:
              $this->_engine = $this->engine_sqlite();
              break;
            case MYSQL:
              $this->_engine = $this->engine_mysql();
              break;
            case PGSQL:
              $this->_engine = $this->engine_pgsql();
              break;
            default;
              $this->_engine = new Engine();
              break;
        }
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
