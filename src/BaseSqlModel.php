<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\crud\WhereTrait;
use PMVC\PlugIn\orm\crud\Create;
use PMVC\PlugIn\orm\crud\Read;
use PMVC\PlugIn\orm\crud\Update;
use PMVC\PlugIn\orm\crud\Delete;
use PMVC\PlugIn\orm\Attrs\Table;

class BaseSqlModel
{
    private $_tableSchema;

    public function create($data): Create
    {
        return new Create($this, '', ['data' => $data]);
    }

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

    public function update($data): Update
    {
      return new Update($this, '', [
        'data' => $data
      ]);
    }

    public function delete($id): Delete
    {
      return new Delete($this, '', [
        'id' => $id
      ]);
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





