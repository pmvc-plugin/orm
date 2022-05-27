<?php

namespace PMVC\Migration\[MIGRATION_PREFIX]_[MIGRATION_NAME];

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Migration';

use PMVC\PlugIn\orm\DAO;
use PMVC\PlugIn\orm\Interfaces\MigrationInterface;

class Migration implements MigrationInterface
{
    public function dependencies()
    {
[MIGRATION_DEP]
    }

    public function process(DAO $dao)
    {
[MIGRATION_PROCESS]
    }
}
