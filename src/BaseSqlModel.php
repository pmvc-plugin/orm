<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;
use PMVC\PlugIn\orm\WhereTrait;
use PMVC\PlugIn\orm\Attrs\Table;

class BaseSqlModel
{
    private $_tableSchema;

    public function getAll(): Read
    {
        return new Read($this, 'all');
    }

    public function getOne(): Read
    {
        return new Read($this, 'one');
    }

    public function getVar(string $key): Read
    {
        return new Read($this, 'var', ['key' => $key]);
    }

    public function create($data): Create
    {
        return new Create($this, '', ['data' => $data]);
    }

    public function update(): Update
    {
    }

    public function delete(): Delete
    {
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

    public function getColumns(): array
    {
        $tableSchema = $this->getSchema();
        \PMVC\d($tableSchema['TABLE_COLUMNS']);
        return [];
    }

    public function getColumnKeys(): array
    {
        $tableSchema = $this->getSchema();
        return array_keys($tableSchema['TABLE_COLUMNS']);
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
          $values[] = $oSql->getBindName($data[$col], $col);  
        }
        
        $sql = \PMVC\plug('orm')->useTpl('insert', [
            'TABLE' => $m->getTableName(),
            'FIELD_KEYS' => implode(', ', $cols),
            'FIELD_VALUES' => implode(', ', $values),
        ]);
        $result = $oSql->set($sql)->process("exec");

        \PMVC\d(compact('cols', 'sql', 'result'));
    }
}

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
     * https://www.w3schools.com/sql/
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

class Update extends Result
{
}

class Delete extends Result
{
}
