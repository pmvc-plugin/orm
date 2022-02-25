<?php

namespace PMVC\PlugIn\orm\Interfaces;

use PMVC\PlugIn\orm\Engine;

interface Behavior
{
    public function accept(Engine $engine);
}
