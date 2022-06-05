<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;
use PMVC\PlugIn\orm\WhereTrait;

class BaseSqlModel
{
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

    public function getSchema()
    {
        $pOrm = \PMVC\plug("orm");
        $table = $pOrm->schema()->fromOneModel(
          $pOrm->parse_model()->fromClass($this)
        );
        return $table->toArray();
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

