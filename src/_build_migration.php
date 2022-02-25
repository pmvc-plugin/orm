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
