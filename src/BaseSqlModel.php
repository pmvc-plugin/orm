<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;
use PMVC\PlugIn\orm\WhereTrait;
use PMVC\PlugIn\orm\Attrs\Table;

class BaseSqlModel
{
    private $_tableSchema;

    public function getAll(): DataList
    {
        return new DataList($this, 'all');
    }

    public function getOne(): DataList
    {
        return new DataList($this, 'one');
    }

    public function getVar(string $key): DataList
    {
        return new DataList($this, 'var', ['key' => $key]);
    }

    public function getSchema(): Table
    {
        if (!$this->_tableSchema) {
            $pOrm = \PMVC\plug('orm');
            $this->_tableSchema = $pOrm
                ->schema()
                ->fromOneModel($pOrm->parse_model()->fromClass($this));
        }
        return $this->_tableSchema;
    }

    public function getTableName(): string
    {
        $tableSchema = $this->getSchema();
        return $tableSchema['TABLE_NAME'];
    }

    public function getSchemaSql(): string
    {
        $tableSchema = $this->getSchema();
        return (string) $tableSchema;
    }

    public function getSchemaArray(): array
    {
        $tableSchema = $this->getSchema();
        return $tableSchema->toArray();
    }
}

class DataList extends HashMap
{
    use WhereTrait;

    private $_model;
    private $_type;
    private $_fields = '*';
    public function __construct(BaseSqlModel $model, string $type, array|null $options = null)
    {
        $this->_model = $model;
        $this->_type = $type;
        if ('var' === $type) {
            $this->_fields = $options['key'];
        }
    }

    /**
     * https://www.w3schools.com/sql/
     * SELECT
     * https://www.sqlite.org/lang_select.html
     */
    public function process()
    {
        $m = $this->_model;
        $oSql = \PMVC\plug('orm')->sql();
        $sql = \PMVC\plug('orm')->useTpl('selectQuery', [
            'FIELD' => $this->_fields,
            'FROM' => $m->getTableName(),
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
