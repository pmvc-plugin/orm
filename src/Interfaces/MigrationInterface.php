<?php

namespace PMVC\PlugIn\orm\Interfaces;

use PMVC\PlugIn\orm\DAO;

interface MigrationInterface 
{
    public function dependencies();
    public function operations(DAO $dao);
}
