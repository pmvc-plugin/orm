<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetDefaultType';

use PMVC\PlugIn\orm\Behaviors\GetColumnType;

class GetDefaultType
{
    public function __invoke($fieldType, $options)
    {
        $result = $this->caller->compile([
            new GetColumnType($fieldType, $options),
        ]);
        return \PMVC\get($result, 0);
    }
}
