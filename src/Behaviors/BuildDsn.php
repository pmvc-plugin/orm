<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildDsn implements Behavior
{
    public $params;
    private $_dsn;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildDsn($this);
    }

    public function setDsn($dsn) {
        $this->_dsn = $dsn;
    }

    public function process()
    {
        return $this->_dsn;
    }
}
