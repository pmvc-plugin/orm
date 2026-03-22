<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildAlterColumn implements Behavior
{
    public $tableName;
    public $fieldName;
    public $newType;
    public $options;
    public $columnTypeSql;
    public $sqls = [];

    public function __construct(string $tableName, string $fieldName, string $newType, array $options = [])
    {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->newType = $newType;
        $this->options = $options;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildAlterColumn($this);
    }

    public function process()
    {
        if (!empty($this->sqls)) {
            return $this->sqls;
        }
        $typeSql = !empty($this->columnTypeSql) ? $this->columnTypeSql : $this->newType;
        $this->sqls[] = "ALTER TABLE {$this->tableName} ALTER COLUMN {$this->fieldName} TYPE {$typeSql}";
        if (array_key_exists('notNull', $this->options)) {
            if ($this->options['notNull']) {
                $this->sqls[] = "ALTER TABLE {$this->tableName} ALTER COLUMN {$this->fieldName} SET NOT NULL";
            } else {
                $this->sqls[] = "ALTER TABLE {$this->tableName} ALTER COLUMN {$this->fieldName} DROP NOT NULL";
            }
        }
        if (array_key_exists('default', $this->options)) {
            if (is_null($this->options['default'])) {
                $this->sqls[] = "ALTER TABLE {$this->tableName} ALTER COLUMN {$this->fieldName} DROP DEFAULT";
            } else {
                $this->sqls[] = "ALTER TABLE {$this->tableName} ALTER COLUMN {$this->fieldName} SET DEFAULT {$this->options['default']}";
            }
        }
        return $this->sqls;
    }
}
