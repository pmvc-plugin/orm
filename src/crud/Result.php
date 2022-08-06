<?php

namespace PMVC\PlugIn\orm\crud;

use PMVC\HashMap;
use PMVC\PlugIn\orm\BaseSqlModel;

class Result extends HashMap
{
    protected $_model;
    protected $_type;
    protected $_fields = '*';
    protected $_options = [];
    public function __construct(
        BaseSqlModel $model,
        string $type,
        array|null $options = null
    ) {
        $this->_model = $model;
        $this->_type = $type;
        if (!empty($options)) {
          $this->_options = $options;
        }
    }
}
