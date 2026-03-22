<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\BehaviorAction';

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Behaviors\BuildTableSql;
use PMVC\PlugIn\orm\Behaviors\BuildColumnSql;
use PMVC\PlugIn\orm\Behaviors\BuildTableArray;
use PMVC\PlugIn\orm\Behaviors\BuildColumnArray;
use PMVC\PlugIn\orm\Behaviors\BuildDsn;
use PMVC\PlugIn\orm\Behaviors\CheckTableExists;
use PMVC\PlugIn\orm\Behaviors\GetColumnType;
use PMVC\PlugIn\orm\Behaviors\GetSequenceResetSql;
use PMVC\PlugIn\orm\Behaviors\BuildDropTable;
use PMVC\PlugIn\orm\Behaviors\BuildAddColumn;
use PMVC\PlugIn\orm\Behaviors\BuildDropColumn;
use PMVC\PlugIn\orm\Behaviors\BuildAlterColumn;
use PMVC\PlugIn\orm\Behaviors\BuildRenameColumn;
use PMVC\PlugIn\orm\Behaviors\BuildRenameTable;
use PMVC\PlugIn\orm\Behaviors\BuildCreateIndex;
use PMVC\PlugIn\orm\Behaviors\BuildDropIndex;
use PMVC\PlugIn\orm\Behaviors\BuildAddConstraint;
use PMVC\PlugIn\orm\Behaviors\BuildDropConstraint;
use DomainException;

class BehaviorAction
{
    private $_engine;

    public function __construct()
    {
        $this->_engine = new Engine();
    }

    public function __invoke(?Engine $engine = null)
    {
        if (!is_null($engine)) {
            $this->_engine = $engine;
        }

        return $this;
    }

    public function tableToArray(Table $table)
    {
        $table['OPTION_LIST'] = null;
        $table['OPTION_COLS'] = null;
        $res = $this->compile([
            new BuildColumnArray($table),
            new BuildTableArray($table),
        ]);

        return end($res);
    }

    public function tableToSql(Table $table)
    {
        $table['OPTION_LIST'] = null;
        $res = $this->compile([
            new BuildColumnSql($table),
            new BuildTableSql($table),
        ]);

        return end($res);
    }

    public function tableExists(string $tableName)
    {
        $res = $this->compile([new CheckTableExists($tableName)]);

        return end($res);
    }

    public function getColumnType(string $fieldType, array $options, $setter = null)
    {
        $res = $this->compile([new GetColumnType($fieldType, $options, $setter)]);

        return end($res);
    }

    public function buildDsn()
    {
        $res = $this->compile([new BuildDsn()]);

        return end($res);
    }

    public function getSequenceResetSql()
    {
        $res = $this->compile([new GetSequenceResetSql()]);

        return end($res);
    }

    public function getEngine(): Engine
    {
        return $this->_engine;
    }

    public function dropTable(string $tableName)
    {
        $res = $this->compile([new BuildDropTable($tableName)]);
        return end($res);
    }

    public function addColumn(string $tableName, string $fieldName, string $fieldType, array $options = [])
    {
        $res = $this->compile([new BuildAddColumn($tableName, $fieldName, $fieldType, $options)]);
        return end($res);
    }

    public function dropColumn(string $tableName, string $fieldName)
    {
        $res = $this->compile([new BuildDropColumn($tableName, $fieldName)]);
        return end($res);
    }

    public function alterColumn(string $tableName, string $fieldName, string $newType, array $options = [])
    {
        $res = $this->compile([new BuildAlterColumn($tableName, $fieldName, $newType, $options)]);
        return end($res);
    }

    public function renameColumn(string $tableName, string $oldName, string $newName)
    {
        $res = $this->compile([new BuildRenameColumn($tableName, $oldName, $newName)]);
        return end($res);
    }

    public function renameTable(string $oldTableName, string $newTableName)
    {
        $res = $this->compile([new BuildRenameTable($oldTableName, $newTableName)]);
        return end($res);
    }

    public function createIndex(string $tableName, string $indexName, array $columns, bool $unique = false)
    {
        $res = $this->compile([new BuildCreateIndex($tableName, $indexName, $columns, $unique)]);
        return end($res);
    }

    public function dropIndex(string $indexName)
    {
        $res = $this->compile([new BuildDropIndex($indexName)]);
        return end($res);
    }

    public function addConstraint(string $tableName, string $constraintName, string $constraintDef)
    {
        $res = $this->compile([new BuildAddConstraint($tableName, $constraintName, $constraintDef)]);
        return end($res);
    }

    public function dropConstraint(string $tableName, string $constraintName)
    {
        $res = $this->compile([new BuildDropConstraint($tableName, $constraintName)]);
        return end($res);
    }

    public function compile(array $behaviors, ?Engine $engine = null)
    {
        if (is_null($engine)) {
            $engine = $this->_engine;
        }
        $res = [];
        $nextBehavior = null;
        foreach ($behaviors as $index => $behavior) {
            $prevBehavior = $nextBehavior;
            $nextBehavior = $behavior->accept($engine);
            if (!$nextBehavior instanceof Behavior) {
                throw new DomainException('Not get behavior accept object');
            }
            $res[$index] = $nextBehavior->process($prevBehavior, $res, $index);
        }
        return $res;
    }
}
