<?php

namespace PMVC\PlugIn\orm;

use PMVC\HashMap;

class Tables extends HashMap
{
    public function toArray()
    {
        $arr = [];
        foreach ($this->state as $tKey => $tVal) {
            $arr[$tKey] = $tVal->toArray();
        }
        return $arr;
    }
}
