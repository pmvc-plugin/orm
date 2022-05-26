<?php

namespace PMVC\PlugIn\orm;

/**
 * @doc https://phpdelusions.net/pdo_examples/update
 */
trait BindTrait 
{
    private $_bindData = [];
    protected $bindKey;

    private function _getBindNum()
    {
        static $i = 0;
        return $i++;
    }

    public function getBindName($v)
    {
        $num = $this->_getBindNum();
        $keyName = $this->bindKey.'_'.$num;
        $this->_bindData[$keyName] = $v;
        return ':'.$keyName;
    }

    public function getBindData()
    {
        return $this->_bindData;
    }
}
