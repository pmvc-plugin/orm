<?php

namespace PMVC\PlugIn\orm\crud;

use PMVC\PlugIn\orm\crud\Result;

class Update extends Result
{
    use WhereTrait;

    /**
     * https://www.w3schools.com/sql/sql_update.asp 
     *
     * SQL UPDATE Statement 
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
            $bindKey = $oSql->getBindName($data[$col], $col);
            $values[] = $col.'='.$bindKey;
          }
        }
        
        $sql = \PMVC\plug('orm')->useTpl('update', [
            'TABLE' => $m->getTableName(),
            'FIELDS' => implode(', ', $values),
            'WHERE' => $this->getWhere($oSql),
        ]);

        $result = $oSql->set($sql)->process("exec");
        return $result;
    }
}
