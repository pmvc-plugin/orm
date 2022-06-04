<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetDefaultType';


class GetDefaultType
{
    /**
     * https://www.sqlite.org/datatype3.html
     */
    private $_baseTypes = [
      'BaseBlob' => [
          'type' => 'blob',
          'field' => 'BinaryField',
        ],
        'BaseInteger' => [
          'type' => 'int',
          'field' => 'IntegerField',
        ],
        'BaseNumeric' => [
          'type' => 'numeric',
          'field' => 'DecimalField',
        ],
        'BaseReal' => [
          'type' => 'real',
          'field' => 'FloatField',
        ],
        'BaseText' => [
          'type' => 'text',
          'field' => 'TextField',
        ],
    ];

    public $engineTypes;

    public function __invoke($fieldType, $options)
    {
        $options['setter'] = $this;
        $type = \PMVC\plug('orm')->behavior()->getColumnType($fieldType, $options); 
        if (!$type) {
            if (!empty($options['baseType'])) {
                $typeMap = \PMVC\get($this->_baseTypes, $options['baseType']);
                if (!empty($this->engineTypes)) {
                    $type = $this->engineTypes[$typeMap['field']];
                }
                if (empty($type)) {
                    $type = $typeMap['type'];
                }
            } else {
                return \PMVC\triggerJson("Not found type", compact('fieldType', 'options'));
            }
        }
        return $type;
    }
}
