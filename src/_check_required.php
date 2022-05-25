<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\CheckRequired';

use InvalidArgumentException;

class CheckRequired
{
    public function __invoke($obj, $shouldRequired)
    {
        foreach ($shouldRequired as $key) {
            if (!isset($obj[$key])) {
                throw new InvalidArgumentException(
                    'Required field [' . $key . '] not set.'
                );
            }
        }
        return true;
    }
}
