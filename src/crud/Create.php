<?php

namespace PMVC\PlugIn\orm\crud;

use PMVC\PlugIn\orm\crud\Result;

class Create extends Result
{
    /**
     * https://www.w3schools.com/sql/sql_insert.asp
     *
     * INSERT INTO
     */
    public function process()
    {
        $m = $this->_model;
        $cols = $m->getColumnKeys();
        $values = [];
        $data = $this->_options['data'];
        $oSql = \PMVC\plug('orm')->sql();
        foreach($cols as $col) {
          if (isset($data[$col])) {
            $values[] = $oSql->getBindName($data[$col], $col);  
          }
        }
        
        $sql = \PMVC\plug('orm')->useTpl('insert', [
            'TABLE' => $m->getTableName(),
            'FIELD_KEYS' => implode(', ', $cols),
            'FIELD_VALUES' => implode(', ', $values),
        ]);
        $result = $oSql->set($sql)->process("exec");
        return $result;
    }
}
