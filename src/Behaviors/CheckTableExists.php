<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class CheckTableExists implements Behavior
{
    private $_exists;

    public function accept(Engine $engine)
    {
        return $engine->checkTableExists($this);
    }

    public function setExists($bool)
    {
        $this->_exists = $bool;
    }

    public function process()
    {
        return $this->_exists;
    }
}
