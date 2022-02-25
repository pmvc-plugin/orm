<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\ParseModel';

class ParseModel {

    const table = 'PMVC\PlugIn\orm\Attrs\Table';
    const field = 'PMVC\PlugIn\orm\Attrs\Field';
    const column = 'PMVC\PlugIn\orm\Attrs\Column';

    public function __invoke(string $fileName) {
        $r = \PMVC\l($fileName, _INIT_CONFIG);
        $class = \PMVC\value($r, ['var', _INIT_CONFIG, _CLASS]);
        $annotation = \PMVC\plug('annotation');
        $attrs = $annotation->getAttrs($class);
        $table = \PMVC\value($attrs, ['obj', self::table, 0]);
        $fields = \PMVC\value($attrs, ['obj', self::field], []); 
        $cols = \PMVC\value($attrs, ['obj', self::column], []); 
        $all = [];
        $fieldMap = [];
        $colMap = [];
        foreach ($fields as $field) {
          $all[$field->name] = $field;
          $fieldMap[$field->name] = $field;
        }
        foreach ($cols as $col) {
          $all[$col->name] = $col;
          $colMap[$col->name] = $col;
        }
        return compact('table', 'fieldMap', 'colMap', 'all');
    }
}

