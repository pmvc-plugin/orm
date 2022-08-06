<?php

namespace PMVC\PlugIn\orm\crud;

use PMVC\PlugIn\orm\crud\Result;

class Delete extends Result
{
    use WhereTrait;

    /**
     * https://www.w3schools.com/sql/sql_delete.asp 
     *
     * SQL DELETE Statement 
     */
    public function process()
    {
        $m = $this->_model;
        $id = $this->_options['id'];
        if (isset($id) && strlen($id)) {
          $this->exact('id', $id);
        }
        $oSql = \PMVC\plug('orm')->sql();
        
        $sql = \PMVC\plug('orm')->useTpl('delete', [
            'TABLE' => $m->getTableName(),
            'WHERE' => $this->getWhere($oSql),
        ]);
        var_dump($sql);
        $result = $oSql->set($sql)->process("exec");
        return $result;
    }
}
