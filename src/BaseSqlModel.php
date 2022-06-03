<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;

class BaseSqlModel {
  
  protected function initData($data) {
      if (is_null($data)) {
          $data = new DataList();
      }
      return $data;
  } 

  public function where($op = "and", $data = null) {
      $data = $this->initData($data);
      return $data;
  }

  public function setMultiWhere($op = "and", $data = null) {
      $data = $this->initData($data);
      return $data;
  }
};

class DataList extends HashMap {
    private $_model;
    public function __construct($model) {
        $this->_model = $model;
        $this['where'] = new Where($op);
    }
}

class Where extends HashMap {

}
