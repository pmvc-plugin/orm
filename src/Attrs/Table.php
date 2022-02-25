<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\HashMap;
use PMVC\PlugIn\orm\BindTrait;
use PMVC\PlugIn\orm\Behaviors\BuildTableSql;
use PMVC\PlugIn\orm\Behaviors\BuildColumnSql;
use PMVC\PlugIn\orm\Behaviors\BuildTableArray;
use PMVC\PlugIn\orm\Behaviors\BuildColumnArray;

#[Attribute]
class Table extends HashMap
{
    use BindTrait;

    public $dao;

    public function __construct($v, $dao = null)
    {
        if (is_string($v)) {
            $v = ['TABLE_NAME' => $v];
        }
        if (!is_null($dao)) {
            $this->dao = $dao;
        }
        parent::__construct($v);
    }

    public function column($name, $type, array $columnOptions = [])
    {
        switch ($type) {
            default:
                $nextColumn = new Column($name, $type, $columnOptions);
                break;
        }
        $this->addColumn($nextColumn);
        return $this;
    }

    public function addColumn(Column $column)
    {
        if ($column->verifySpec()) {
            $this['TABLE_COLUMNS'][$column->name] = $column;
        }
    }

    protected function getInitialState()
    {
        return [
            'TABLE_COLUMNS' => [],
        ];
    }

    public function getAllRequired()
    {
        return ['TABLE_NAME', 'TABLE_COLUMNS'];
    }

    public function getAllOptional()
    {
        return ['PRIMARY_KEY'];
    }

    public function getSupported()
    {
        return array_merge($this->getAllRequired(), $this->getAllOptional());
    }

    public function commit()
    {
      return $this->dao->commit(
        $this->__toString(),
        $this->getBindData()
      );
    }

    public function toArray()
    {
        $this['OPTION_LIST'] = null;
        $this['OPTION_COLS'] = null;
        $res = \PMVC\plug('orm')->compile([
            new BuildColumnArray($this),
            new BuildTableArray($this),
        ]);

        return $res[count($res) - 1];
    }

    public function __toString()
    {
        $this['OPTION_LIST'] = null;
        $res = \PMVC\plug('orm')->compile([
            new BuildColumnSql($this),
            new BuildTableSql($this),
        ]);

        return $res[count($res) - 1];
    }
}
