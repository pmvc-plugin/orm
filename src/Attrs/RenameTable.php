<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\HashMap;

#[Attribute]
class RenameTable extends HashMap
{
  public function __construct($nextName)
  {
    $this["next"] = $nextName;
  }
}
