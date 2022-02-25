<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetDAO';

use PMVC\PlugIn\orm\DAO;

class GetDAO
{
    private $_dao;

    public function __invoke()
    {
        return $this;
    }

    public function getDefault()
    {
        if (empty($this->_dao)) {
          $this->_dao = $this->getDao('');
        }
        return $this->_dao;
    }

    public function getDao($mode)
    {
        switch($mode) {
            case 'structure':
              $o = new StructureDAO();
              break;
            default:
              $o = new DAO();
              break;
        }
        return $o;
    }

}

class StructureDao extends DAO {
    public function commit($sql, array $prepare = []) {

    }

    public function process() {

    }
}
