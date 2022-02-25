<?php
namespace PMVC\PlugIn\orm;

use PMVC\TestCase;

class OrmTest extends TestCase
{
    private $_plug = 'orm';
    function testPlugin()
    {
        ob_start();
        print_r(\PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->haveString($this->_plug,$output);
    }

}
