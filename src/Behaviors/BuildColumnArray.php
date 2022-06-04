<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Fields\AutoField;
use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;
use PMVC\HashMap;
use DomainException;

class BuildColumnArray implements Behavior
{
    // override in db engine
    public $strAutoIncrement = 'AUTO_INCREMENT';

    public function __construct($table)
    {
        $this->_table = $table;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildColumn($this);
    }

    protected function setRow(&$row, $key, $val)
    {
        $row[$key] = \PMVC\get($val, 0, $val);
    }

    private function _transform($key)
    {
        if (isset($this->transform[$key])) {
            return $this->transform[$key];
        }
        return $key;
    }

    protected function processRow($col, $keep = null, $primary = null)
    {
        $rowName = $col['name'];
        $row = [];
        $this->setRow($row, 'name', $rowName);
        $this->setRow($row, 'type', $col['type']);
        if ($primary) {
            if (count($primary) === 1) {
                if ($primary[0] === $rowName) {
                    $this->setRow($row, 'primaryKey', [true, 'PRIMARY KEY']);
                }
            } else {
                if (in_array($colName, $primary)) {
                    $keep['primaryArr'][] = $rowName;
                }
            }
        } elseif ($col['primaryKey']) {
            $this->setRow($row, 'primaryKey', [true, 'PRIMARY KEY']);
        }
        if ($col['autoIncrement']) {
            $this->setRow(
                $row,
                'autoIncrement',
                [
                  true,
                  \PMVC\get($this->transform, 'AUTO_INCREMENT')
                ]
            );
        }
        if ($col['notNull']) {
            $this->setRow($row, 'notNull', 'NOT NULL');
        }
        if ($col['unique']) {
            $this->setRow($row, 'unique', 'UNIQUE');
        }
        if ($col['default']) {
            if ('BaseText' === $col['baseType']) {
                $bindName = $this->_table->getBindName($col['default']);
                $nextDefaultValue = "'" . $bindName . "'";
            } else {
                $nextDefaultValue = $this->_transform($col['default']);
            }
            $this->setRow($row, 'default', 'DEFAULT ' . $nextDefaultValue);
        }
        return compact('row', 'rowName');
    }

    protected function processBase()
    {
        $columns = $this->_table['TABLE_COLUMNS'];
        $primary =
            $this->_table['PRIMARY'] && count($this->_table['PRIMARY'])
                ? $this->_table['PRIMARY']
                : null;
        $keep = new HashMap(['primaryArr' => [], 'hasPrimary' => false]);
        $allRowSeq = [];
        $allRowMap = [];
        foreach ($columns as $col) {
            extract(
                \PMVC\assign(
                    ['row', 'rowName'],
                    $this->processRow($col, $keep, $primary)
                )
            );
            $allRowSeq[] = join(' ', $row);
            $allRowMap[$rowName] = $row;
        }
        extract(\PMVC\assign(['hasPrimary', 'primaryArr'], \PMVC\get($keep)));
        if (!$hasPrimary) {
            if (isset($allRowMap['id'])) {
                throw DomainException(
                    'You need handle primaryKey by yourself when id column exists'
                );
            }
            $autoPrimary = $this->processRow(new AutoField('id'));
            array_unshift($allRowSeq, join(' ', $autoPrimary['row']));
            $allRowMap[$autoPrimary['rowName']] = $autoPrimary['row'];
        }
        return compact('allRowSeq', 'allRowMap', 'primaryArr');
    }

    public function process()
    {
        extract(
            \PMVC\assign(['allRowMap', 'primaryArr'], $this->processBase())
        );
        $opList = [];
        if (!empty($primaryArr)) {
            $opList['primaryKey'] =
                'PRIMARY KEY (' . join(', ', $primaryArr) . ')';
        }
        $this->_table['OPTION_LIST'] = $opList;
        $this->_table['OPTION_COLS'] = $allRowMap;
    }
}
