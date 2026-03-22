<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Tables;

class StructureDAO extends DAO
{
    private $_tables = null;

    private function _initTables()
    {
        if (is_null($this->_tables)) {
            $this->_tables = new Tables();
        }
    }

    public function createModel($tableName)
    {
        $this->_initTables();
        if (empty($this->_tables[$tableName])) {
            $this->_tables[$tableName] = new Table($tableName, $this);
        }
        return $this->_tables[$tableName];
    }

    public function deleteModel(string $tableName): DAO
    {
        $this->_initTables();
        unset($this->_tables[$tableName]);
        return $this;
    }

    public function renameModel(string $oldTableName, string $newTableName): DAO
    {
        $this->_initTables();
        if (isset($this->_tables[$oldTableName])) {
            $table = $this->_tables[$oldTableName];
            $table->setTableName($newTableName);
            $this->_tables[$newTableName] = $table;
            unset($this->_tables[$oldTableName]);
        }
        return $this;
    }

    public function addField(string $tableName, string $fieldName, string $fieldType, array $options = []): DAO
    {
        $this->_initTables();
        if (isset($this->_tables[$tableName])) {
            $this->_tables[$tableName]->column($fieldName, $fieldType, $options);
        }
        return $this;
    }

    public function removeField(string $tableName, string $fieldName): DAO
    {
        $this->_initTables();
        if (isset($this->_tables[$tableName])) {
            $columns = $this->_tables[$tableName]['TABLE_COLUMNS'];
            if (isset($columns[$fieldName])) {
                unset($columns[$fieldName]);
                $this->_tables[$tableName]['TABLE_COLUMNS'] = $columns;
            }
        }
        return $this;
    }

    public function alterField(string $tableName, string $fieldName, string $newType, array $options = []): DAO
    {
        $this->_initTables();
        if (isset($this->_tables[$tableName])) {
            $columns = $this->_tables[$tableName]['TABLE_COLUMNS'];
            if (isset($columns[$fieldName])) {
                $col = $columns[$fieldName];
                $col['type'] = $newType;
                if (array_key_exists('notNull', $options)) {
                    $col['notNull'] = $options['notNull'];
                }
                if (array_key_exists('default', $options)) {
                    $col['default'] = $options['default'];
                }
                $columns[$fieldName] = $col;
                $this->_tables[$tableName]['TABLE_COLUMNS'] = $columns;
            }
        }
        return $this;
    }

    public function renameField(string $tableName, string $oldName, string $newName): DAO
    {
        $this->_initTables();
        if (isset($this->_tables[$tableName])) {
            $columns = $this->_tables[$tableName]['TABLE_COLUMNS'];
            if (isset($columns[$oldName])) {
                $col = $columns[$oldName];
                $col->name = $newName;
                $col['name'] = $newName;
                $columns[$newName] = $col;
                unset($columns[$oldName]);
                $this->_tables[$tableName]['TABLE_COLUMNS'] = $columns;
            }
        }
        return $this;
    }

    public function toArray()
    {
        if (is_null($this->_tables)) {
            return [];
        }
        return $this->_tables->toArray();
    }

    public function commit($sql, array $prepare = []) : DAO
    {
        return $this;
    }

    public function process()
    {
    }
}
