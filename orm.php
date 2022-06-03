<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\orm';

use PMVC\PlugIn;

const SQLITE = 'sqlite';
const MYSQL = 'mysql';
const PGSQL = 'pgsql';

const DATABASES = 'databases';
const DEFAULT_KEY = 'default';
const TYPE = 'type';
const THIS_PLUGIN = 'orm';

class orm extends PlugIn
{

    public function init()
    {
    }

    public function setEngine($databaseId = DEFAULT_KEY)
    {
        $configs = \PMVC\value($this, [DATABASES, $databaseId]);
        $type = \PMVC\get($configs, TYPE);
        switch ($type) {
            case SQLITE:
                $engine = $this->engine_sqlite($configs);
                break;
            case MYSQL:
                $engine = $this->engine_mysql($configs);
                break;
            case PGSQL:
                $engine = $this->engine_pgsql($configs);
                break;
            default:
                $engine = new Engine($configs);
                break;
        }
        $engine[TYPE] = $type;
        $this->behavior($engine);
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
