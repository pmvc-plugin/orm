<?php

namespace PMVC\PlugIn\orm;

trait WhereTrait
{
    public function exact()
    {
    }

    public function iexact()
    {
    }

    public function contains()
    {
    }

    public function icontains()
    {
    }

    public function regex()
    {
    }

    public function iregex()
    {
    }

    public function gt()
    {
    }

    public function gte()
    {
    }

    public function lt()
    {
    }

    public function lte()
    {
    }

    public function startswith()
    {
    }

    public function istartswith()
    {
    }

    public function endswith()
    {
    }

    public function iendswith()
    {
    }

    public function where($op = 'and', $data = null)
    {
        $data = $this->initData($data);
        return $data;
    }

    public function setMultiWhere($op = 'and', $data = null)
    {
        $data = $this->initData($data);
        return $data;
    }
}