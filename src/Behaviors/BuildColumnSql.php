<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Engine;

class BuildColumnSql extends BuildColumnArray
{

    public function accept(Engine $engine)
    {
        return $engine->buildColumn($this);
    }

    protected function setRow(&$row, $key, $val)
    {
        $row[] = \PMVC\get($val, 1, $val);
    }

    public function process()
    {
        extract(
            \PMVC\assign(
                ['allRowSeq', 'primaryArr'],
                $this->processBase()
            )
        );
        $opList = $allRowSeq;
        if (!empty($primaryArr)) {
            $opList[] = 'PRIMARY KEY ('.join(', ', $primaryArr).')';
        }

        $this->_table['OPTION_LIST'] = "\t" . join(", \n\t", $opList);
    }
}
