<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;
use PMVC\PlugIn\orm\WhereTrait;

class BaseSqlModel
{
    use WhereTrait;

    protected function initData($data)
    {
        if (is_null($data)) {
            $data = new DataList();
        }
        return $data;
    }

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
}

class DataList extends HashMap
{
    private $_model;
    private $_type;
    public function __construct(BaseSqlModel $model, string $type)
    {
        $this->_model = $model;
        $this->_type = $type;
    }
}

