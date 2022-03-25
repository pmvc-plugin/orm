<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Diff';

class Diff
{
    public function __invoke()
    {
        return $this;
    }


    public function diffAll($left, $right)
    {

      \PMVC\d(compact('left', 'right'));
    }


    public function diffTable($left, $right)
    {
    }
}
