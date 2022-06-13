<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\HashMap;
use PMVC\PlugIn\orm\DAO;
use PMVC\PlugIn\orm\BindTrait;
use DomainException;

#[Attribute]
class Table extends HashMap
{
    use BindTrait;

    private $_dao;

    public function __construct($v = [], Dao $dao = null)
    {
        if (is_string($v)) {
            $v = ['TABLE_NAME' => $v];
        }
        if (!is_null($dao)) {
            $this->setDao($dao);
        }
        parent::__construct($v); // for trigger getInitialState()
    }

    protected function getInitialState()
    {
        return [
            'TABLE_COLUMNS' => [],
        ];
    }

    public function setTableName(string $name): Table
    {
        $this['TABLE_NAME'] = $name;
        return $this;
    }

    public function setDao(Dao $dao): Table
    {
        $this->_dao = $dao;
        return $this;
    }

    public function column($name, $type, array $columnOptions = []): Table
    {
        switch ($type) {
            default:
                $nextColumn = new Column($name, $type, $columnOptions);
                break;
        }
        return $this->addColumn($nextColumn);
    }

    public function addColumn(Column $column): Table
    {
        if ($column->verifySpec()) {
            $this['TABLE_COLUMNS'][$column->name] = $column;
        }
        return $this;
    }

    public function getAllRequired(): array
    {
        return ['TABLE_NAME', 'TABLE_COLUMNS'];
    }

    public function getAllOptional(): array
    {
        return ['PRIMARY_KEY'];
    }

    public function getSupported()
    {
        return array_merge($this->getAllRequired(), $this->getAllOptional());
    }

    public function commit(): DAO
    {
        if (empty($this->_dao)) {
            throw new DomainException('Not setup dao, use setDao to do it.');
        }
        return $this->_dao->commit($this->__toString(), $this->getBindData());
    }

    public function toArray(): array
    {
        return \PMVC\plug('orm')
            ->behavior()
            ->tableToArray($this);
    }

    public function __toString(): string
    {
        return \PMVC\plug('orm')
            ->behavior()
            ->tableToSql($this);
    }
}
