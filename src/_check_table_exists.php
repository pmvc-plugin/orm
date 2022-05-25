<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\CheckTableExists';

class CheckTableExists
{
    public function __invoke($name)
    {
        return $this;
    }
}
