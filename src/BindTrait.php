<?php

namespace PMVC\PlugIn\orm;

/**
 * @doc https://phpdelusions.net/pdo_examples/update
 */
trait BindTrait 
{
    private $_bindData = [];

    private function _getBindNum()
    {
        static $i = 0;
        return $i++;
    }

    public function getBindName($v, $name='')
    {
        $num = $this->_getBindNum();
        $keyName = $name.'_'.$num;
        $this->_bindData[$keyName] = $v;
        return ':'.$keyName;
    }

    public function getBindData()
    {
        return $this->_bindData;
    }
}
