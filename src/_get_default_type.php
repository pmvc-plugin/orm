<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetDefaultType';

use PMVC\PlugIn\orm\Behaviors\GetColumnType;

class GetDefaultType
{
    private $_baseTypes = [
        'BaseText' => 'text',
        'BaseInteger' => 'int',
    ];

    public function __invoke($fieldType, $options)
    {
        $result = $this->caller->compile([
            new GetColumnType($fieldType, $options),
        ]);
        return \PMVC\get($result, 0, function () use ($fieldType, $options) {
            if (!empty($options['baseType'])) {
                return \PMVC\get($this->_baseTypes, $options['baseType']);
            } else {
                \PMVC\triggerJson("Not found type", compact('fieldType', 'options'));
            }
        });
    }
}
