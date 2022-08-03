<?php

namespace PMVC\PlugIn\orm;

trait WhereTrait
{
    private $_where;
    private $_whereType;

    protected function setWhere($op, $col, $val)
    {
        $this->_where[] = [$op, $col, $val];
    }

    public function exact($col, $val)
    {
        $this->setWhere('exact', $col, $val);
    }

    public function iexact($col, $val)
    {
        $this->setWhere('iexact', $col, $val);
    }

    public function contains($col, $val)
    {
        $this->setWhere('contains', $col, $val);
    }

    public function icontains($col, $val)
    {
        $this->setWhere('icontains', $col, $val);
    }

    public function regex($col, $val)
    {
        $this->setWhere('regex', $col, $val);
    }

    public function iregex($col, $val)
    {
        $this->setWhere('iregex', $col, $val);
    }

    public function gt($col, $val)
    {
        $this->setWhere('gt', $col, $val);
    }

    public function gte($col, $val)
    {
        $this->setWhere('gte', $col, $val);
    }

    public function lt($col, $val)
    {
        $this->setWhere('lt', $col, $val);
    }

    public function lte($col, $val)
    {
        $this->setWhere('lte', $col, $val);
    }

    public function startswith($col, $val)
    {
        $this->setWhere('startswith', $col, $val);
    }

    public function istartswith($col, $val)
    {
        $this->setWhere('istartswith', $col, $val);
    }

    public function endswith($col, $val)
    {
        $this->setWhere('endswith', $col, $val);
    }

    public function iendswith($col, $val)
    {
        $this->setWhere('iendswith', $col, $val);
    }

    public function filter($op = 'and')
    {
        $this->_whereType = $op;
        return $this;
    }

    public function setMultiFilter($opList)
    {
        return $this;
    }
}
