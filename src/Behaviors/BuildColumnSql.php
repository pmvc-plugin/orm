<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildColumnSql implements Behavior
{
    public function __construct($table)
    {
        $this->_table = $table;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildColumn($this);
    }

    public function process()
    {
        $columns = $this->_table['TABLE_COLUMNS'];
        $opList = [];
        $primary = $this->_table['PRIMARY'] && count($this->_table['PRIMARY']) ? $this->_table['PRIMARY'] : null;
        $primaryArr = [];
        foreach ($columns as $col) {
            $colName = $col['name']; 
            $row = [];
            $row[] = $colName;
            $row[] = $col['type'];
            if ($primary) {
                if (count($primary)===1) {
                    if ($primary[0] === $colName) {
                        $row[] = 'PRIMARY KEY';
                    } 
                } else {
                    if (in_array($colName, $primary)) {
                        $primaryArr[] = $colName;
                    }
                }
            }
            if ($col['notNull']) {
                $row[] = 'NOT NULL';
            }
            if ($col['unique']) {
                $row[] = 'UNIQUE';
            }
            if ($col['default']) {
                $bindName = $this->_table->getBindName($col['default']);
                $row[] = 'DEFAULT (' . $bindName . ')';
            }
            $opList[] = join(' ', $row);
        }
        if (!empty($primaryArr)) {
            $opList[] = 'PRIMARY KEY ('.join(', ', $primaryArr).')';
        }
        $this->_table['OPTION_LIST'] = "\t" . join(", \n\t", $opList);
    }
}
