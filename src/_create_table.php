<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\CreateTable';

use PMVC\HashMap;
use PMVC\ListIterator;
use PMVC\PlugIn\orm\Attrs\Table;
use InvalidArgumentException;


const STRINGS = 'strings';
const NUMBERS = 'numbers';
const NUMERICS = 'numerics';
const DATE_TIME = 'date_time';
const OBJECTS = 'objects';
const AUTO_NUMBERS = 'auto_numbers';

class CreateTable
{
    public function __invoke($tableName)
    {
        return new Table($tableName);
    }
}

class ReferentialActions {
    protected function getInitialState()
    {
        return [
          'cascade' => 'CASCADE',
          'restrict' => 'RESTRICT',
          'no_action' => 'NO ACTION',
          'set_null' => 'SET NULL',
          'set_default' => 'SET DEFAULT',
        ];
    }
}

