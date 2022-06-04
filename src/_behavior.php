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
use DomainException;

class BehaviorAction
{
    private $_engine;

    public function __construct()
    {
        $this->_engine = new Engine();
    }

    public function __invoke(Engine $engine = null)
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

    public function compile(array $behaviors, Engine $engine = null)
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
