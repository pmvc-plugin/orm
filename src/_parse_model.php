<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\ParseModel';

class ParseModel
{
    const table = 'PMVC\PlugIn\orm\Attrs\Table';
    const field = 'PMVC\PlugIn\orm\Attrs\Field';
    const column = 'PMVC\PlugIn\orm\Attrs\Column';
    const renameTable = 'PMVC\PlugIn\orm\Attrs\RenameTable';
    const renameColumn = 'PMVC\PlugIn\orm\Attrs\RenameColumn';

    public function __invoke(string $fileName)
    {
        $r = \PMVC\l($fileName, _INIT_CONFIG);
        $class = \PMVC\value($r, ['var', _INIT_CONFIG, _CLASS]);
        $annotation = \PMVC\plug('annotation');
        $attrs = $annotation->getAttrs($class);
        $table = \PMVC\value($attrs, ['obj', self::table, 0]);
        $renameTable = \PMVC\value($attrs, ['obj', self::renameTable, 0]);
        $fields = \PMVC\value($attrs, ['obj', self::field], []);
        $cols = \PMVC\value($attrs, ['obj', self::column], []);
        $renameColumns = \PMVC\value($attrs, ['obj', self::renameColumn], []);
        $allColumns = [];
        $fieldMap = [];
        $colMap = [];
        foreach ($fields as $field) {
            $allColumns[$field->name] = $field;
            $fieldMap[$field->name] = $field;
        }
        foreach ($cols as $col) {
            $allColumns[$col->name] = $col;
            $colMap[$col->name] = $col;
        }
        return compact(
            'table',
            'renameTable',
            'fieldMap',
            'colMap',
            'allColumns',
            'renameColumns'
        );
    }
}
