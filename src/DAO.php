<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Attrs\Table;

/**
 * Migration Operations
 *
 * django:
 *  https://docs.djangoproject.com/en/4.0/ref/migration-operations/
 */
class DAO
{
    private $_history = [];
    private $_queue = [];

    public function commit($sql, array $prepare = []) : DAO
    {
        $this->_queue[] = [$sql, $prepare];
        return $this;
    }

    public function process()
    {
        $resultArr = \PMVC\plug('orm')->pdo()->processDao($this);
        $this->_history = array_merge($this->_history, $resultArr);
        return $resultArr;
    }

    public function getQueue($isClean = false)
    {
        $queue = $this->_queue; 
        if ($isClean) {
            $this->_queue = [];
        }
        return $queue;
    }

    public function getHistory()
    {
        return $this->_history;
    }

    public function createModel($tableName)
    {
        return new Table($tableName, $this);
    }

    public function deleteModel(string $tableName): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->dropTable($tableName);
        return $this->commit($sql);
    }

    public function renameModel(string $oldTableName, string $newTableName): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->renameTable($oldTableName, $newTableName);
        return $this->commit($sql);
    }

    public function addField(string $tableName, string $fieldName, string $fieldType, array $options = []): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->addColumn($tableName, $fieldName, $fieldType, $options);
        return $this->commit($sql);
    }

    public function removeField(string $tableName, string $fieldName): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->dropColumn($tableName, $fieldName);
        return $this->commit($sql);
    }

    public function alterField(string $tableName, string $fieldName, string $newType, array $options = []): DAO
    {
        $result = \PMVC\plug('orm')->behavior()->alterColumn($tableName, $fieldName, $newType, $options);
        if (is_array($result)) {
            foreach ($result as $sql) {
                $this->commit($sql);
            }
            return $this;
        }
        return $this->commit($result);
    }

    public function renameField(string $tableName, string $oldName, string $newName): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->renameColumn($tableName, $oldName, $newName);
        return $this->commit($sql);
    }

    public function addIndex(string $tableName, string $indexName, array $columns, bool $unique = false): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->createIndex($tableName, $indexName, $columns, $unique);
        return $this->commit($sql);
    }

    public function removeIndex(string $indexName): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->dropIndex($indexName);
        return $this->commit($sql);
    }

    public function addConstraint(string $tableName, string $constraintName, string $constraintDef): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->addConstraint($tableName, $constraintName, $constraintDef);
        return $this->commit($sql);
    }

    public function removeConstraint(string $tableName, string $constraintName): DAO
    {
        $sql = \PMVC\plug('orm')->behavior()->dropConstraint($tableName, $constraintName);
        return $this->commit($sql);
    }

    public function runSql(string $sql, array $prepare = []): DAO
    {
        return $this->commit($sql, $prepare);
    }

    public function runPhp(callable $forward, ?callable $reverse = null): DAO
    {
        $forward($this);
        return $this;
    }

    public function alterUniqueTogether(string $tableName, array $columns): DAO
    {
        $constraintName = "{$tableName}_unique_" . join('_', $columns);
        $colStr = join(', ', $columns);
        return $this->addConstraint($tableName, $constraintName, "UNIQUE ({$colStr})");
    }

    public function alterIndexTogether(string $tableName, array $columns): DAO
    {
        $indexName = "{$tableName}_idx_" . join('_', $columns);
        return $this->addIndex($tableName, $indexName, $columns);
    }
}
