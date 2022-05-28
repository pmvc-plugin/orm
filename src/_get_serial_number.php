<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\GetSerialNumber';
use DomainException;

class GetSerialNumber
{
    public function __invoke($migrationFolder)
    {
        return new SN($migrationFolder);
    }
}

class SN
{
    private $_folder;

    public function __construct($folder)
    {
        $this->_folder = $folder;
    }

    public function getNextFileName($name='', $type="auto")
    {
        $nextSN = $this->getNextSN();
        if (1 === (int)$nextSN) {
          $name = 'initial';
        } elseif (empty($name)) {
          $name = $type.'_'.gmdate("Ymd_hi");
        }
        $nextName = $nextSN.'_'.$name;
        $nextFile = this->_folder.'/'.$nextSN.'_'.$name.'.php';
        $lastName = $this->getLastName();
        return compact('nextName', 'nextFile', 'lastName');
    }

    public function getNextSN()
    {
        $last = $this->getLastSN();
        $next = ++$last;
        return sprintf('%04d', $next);
    }

    public function getLastName()
    {
        $list = $this->_getFileList();
        $last = end($list);
        return $last;
    }

    public function getLastSN()
    {
        $last = $this->getLastName();
        preg_match("/(\d+).*/", $last, $matches);
        $lastSN = \PMVC\get($matches, 1);
        if (!is_numeric($lastSN)) { 
          throw new DomainException('Get last sn failed. ['.$last.', '.$lastSN.']');
        } else {
          return (int)$lastSN;
        }
    }

    private function _getFileList()
    {
        $files = glob($this->_folder.'/[0-9]*.php');
        $list = [];
        foreach ($files as $f) {
          $list[] = basename($f, ".php");
        }
        natsort($list);
        return $list;
    }
}
