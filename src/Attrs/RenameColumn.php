<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\HashMap;

#[Attribute]
class RenameColumn extends HashMap
{
    public function __construct($preName, $nextName)
    {
      $this['prev'] = $preName;
      $this['next'] = $nextName;
    }
}
