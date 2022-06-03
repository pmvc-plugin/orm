<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;
use PMVC\PlugIn\orm;

class BuildDsn implements Behavior
{
    public $params;
    private $_dsn;
    private $_engine;

    public function accept(Engine $engine)
    {
        $this->_engine = $engine;
        return $engine->buildDsn($this);
    }

    public function setDsn($dsn)
    {
        $this->_dsn = $dsn;
    }

    private function _checkRequireField(Engine $engine)
    {
        return \PMVC\plug('orm')->check_required(
            $engine,
            $engine->getAllDsnRequired()
        );
    }

    public function stringify(Engine $engine)
    {
        $allFields = $engine->getAllDsnFields();
        $arr = [];
        foreach ($allFields as $field) {
            $val = $engine[$field];
            if (!empty($val)) {
                $arr[$field] = $engine[$field];
            }
        }
        return http_build_query($arr, '', ';');
    }

    public function process()
    {
        $this->_checkRequireField($this->_engine);
        $dsn = is_string($this->_dsn)
            ? $this->_dsn
            : $this->stringify($this->_dsn);
        return $this->_engine[orm\TYPE] . ':' . $dsn;
    }
}
