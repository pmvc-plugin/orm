<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;
use PMVC\PlugIn\orm\WhereTrait;

class BaseSqlModel
{
    private $_tableSchema;

    public function getAll()
    {
        return new DataList($this, 'all');
    }

    public function getOne()
    {
        return new DataList($this, 'one');
    }

    public function getVar()
    {
        return new DataList($this, 'var');
    }

    public function getTableSchema()
    {
        if (!$this->_tableSchema) {
            $pOrm = \PMVC\plug('orm');
            $this->_tableSchema = $pOrm
                ->schema()
                ->fromOneModel($pOrm->parse_model()->fromClass($this));
        }
        return $this->_tableSchema;
    }

    public function getSchemaSql(): string
    {
        $tableSchema = $this->getTableSchema();
        return (string) $tableSchema;
    }

    public function getSchemaArray(): array
    {
        $tableSchema = $this->getTableSchema();
        return $tableSchema->toArray();
    }
}

class DataList extends HashMap
{
    use WhereTrait;

    private $_model;
    private $_type;
    public function __construct(BaseSqlModel $model, string $type)
    {
        $this->_model = $model;
        $this->_type = $type;
    }
}
