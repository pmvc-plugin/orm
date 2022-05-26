<?php

/**
 * Not start to use 
 */

namespace PMVC\PlugIn\orm\Attrs;
use PMVC\HashMap;

#[Attribute]
class Relation extends HashMap
{

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
