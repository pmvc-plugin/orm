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

    public function __invoke()
    {
        return $this;
    }

    public function fromFile(string $fileName)
    {
        $importFrom = \PMVC\l($fileName);
        $class = \PMVC\importClass($importFrom);
        return $this->fromClass($class);
    }

    public function fromClass($class)
    {
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
            $oField = $field->getField();
            $fieldMap[$oField->name] =
            $allColumns[$oField->name] = $oField;
        }
        foreach ($cols as $col) {
            $colMap[$col->name] = $col;
            $allColumns[$col->name] = $col;
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
