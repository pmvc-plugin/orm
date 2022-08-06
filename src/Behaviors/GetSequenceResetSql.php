<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;
use PMVC\PlugIn\orm;

class GetSequenceResetSql implements Behavior 
{
    public function accept(Engine $engine)
    {
        $this->_engine = $engine;
        return $engine->getSequenceResetSql($this);
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

