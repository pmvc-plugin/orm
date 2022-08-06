<?php

namespace PMVC\PlugIn\orm\crud;

use PMVC\PlugIn\orm\crud\Result;

class Read extends Result
{
    use WhereTrait;

    public function __construct(
        BaseSqlModel $model,
        string $type,
        array|null $options = null
    ) {
        parent::__construct($model, $type, $options);
        if ('var' === $type) {
            $this->_fields = $options['key'];
        }
    }

    /**
     * https://www.w3schools.com/sql/sql_select.asp 
     *
     * SELECT
     * https://www.sqlite.org/lang_select.html
     */
    public function process()
    {
        $m = $this->_model;
        $oSql = \PMVC\plug('orm')->sql();
        $sql = \PMVC\plug('orm')->useTpl('selectQuery', [
            'FIELD' => $this->_fields,
            'TABLE' => $m->getTableName(),
            'WHERE' => '',
            'GROUP_BY' => '',
            'ORDER_BY' => '',
            'LIMIT' => '',
        ]);

        $execType = 'var' === $this->_type ? 'one' : $this->_type;
        $result = $oSql->set($sql)->process($execType);
        if ('var' === $this->_type) {
            $result = \PMVC\get($result, $this->_fields);
        }
        return $result;
    }
}
