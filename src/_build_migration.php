<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\BuildMigraton';

class BuildMigraton
{
    public function __invoke()
    {
        return $this;
    }

    public function buildCreateModel($schema)
    {
        extract(
            \PMVC\assign(['createModel', 'addField'], $this->getTpls()),
            EXTR_PREFIX_ALL,
            'tpl'
        );

        $command = '';

        $fieldsCommand = [];
        foreach ($schema['OPTION_COLS'] as $col) {
            // Skip auto-generated id column (auto-added by BuildColumnArray)
            if ($col['name'] === 'id' && isset($col['primaryKey'])) {
                continue;
            }
            $colArr = [
                'FIELD_NAME' => $col['name'],
                'FIELD_TYPE' => $col['type'],
                'FIELD_OPTION' => '[]',
            ];
            $fieldsCommand[] = \PMVC\tplArrayReplace($tpl_addField, $colArr);
        }

        $schema['FIELDS'] = join("\n", $fieldsCommand);

        $command .= \PMVC\tplArrayReplace(
            $tpl_createModel,
            ['TABLE_NAME', 'FIELDS'],
            $schema
        );

        return $command;
    }

    public function buildDeleteModel($tableName)
    {
        return "\n        \$dao->deleteModel('{$tableName}')->process();";
    }

    public function buildAddField($tableName, $field)
    {
        $name = $field['name'];
        $type = $field['type'];
        $options = $this->_buildFieldOptions($field);
        return "\n        \$dao->addField('{$tableName}', '{$name}', '{$type}', {$options})->process();";
    }

    public function buildRemoveField($tableName, $fieldName)
    {
        return "\n        \$dao->removeField('{$tableName}', '{$fieldName}')->process();";
    }

    public function buildAlterField($tableName, $field)
    {
        $name = $field['name'];
        $type = $field['type'];
        $options = $this->_buildFieldOptions($field);
        return "\n        \$dao->alterField('{$tableName}', '{$name}', '{$type}', {$options})->process();";
    }

    public function buildRenameField($tableName, $oldName, $newName)
    {
        return "\n        \$dao->renameField('{$tableName}', '{$oldName}', '{$newName}')->process();";
    }

    public function buildRenameModel($oldName, $newName)
    {
        return "\n        \$dao->renameModel('{$oldName}', '{$newName}')->process();";
    }

    public function buildAddIndex($tableName, $indexName, $columns, $unique = false)
    {
        $colStr = "['" . join("', '", $columns) . "']";
        $uniqueStr = $unique ? 'true' : 'false';
        return "\n        \$dao->addIndex('{$tableName}', '{$indexName}', {$colStr}, {$uniqueStr})->process();";
    }

    public function buildRemoveIndex($indexName)
    {
        return "\n        \$dao->removeIndex('{$indexName}')->process();";
    }

    private function _buildFieldOptions($field)
    {
        $opts = [];
        if (!empty($field['notNull'])) {
            $opts[] = "'notNull' => true";
        }
        if (isset($field['default'])) {
            $default = is_string($field['default']) ? "'{$field['default']}'" : $field['default'];
            $opts[] = "'default' => {$default}";
        }
        if (!empty($field['unique'])) {
            $opts[] = "'unique' => true";
        }
        return '[' . join(', ', $opts) . ']';
    }

    public function getTpls()
    {
        $createModel = <<<'EOT'

        $dao->createModel('[TABLE_NAME]')
[FIELDS]
            ->commit()
            ->process();
EOT;
        $addField = <<<'EOT'
            ->column('[FIELD_NAME]', '[FIELD_TYPE]', [FIELD_OPTION])
EOT;
        return compact('createModel', 'addField');
    }
}
