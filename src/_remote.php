<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\RemoteActions';

use PMVC\PlugIn\orm\BaseSqlModel;
use DomainException;

class RemoteActions
{
    public function __invoke()
    {
        return $this;
    }

    public function getDao()
    {
        return $this->caller->dao()->getDefault();
    }

    public function create($tableNameOrModel)
    {
        $dao = $this->getDao();
        if ($tableNameOrModel instanceof BaseSqlModel) {
            $table = $tableNameOrModel->getSchema();
            $table->setDao($dao);
        } elseif (is_string($tableNameOrModel)) {
            $table = $dao->createModel($tableNameOrModel);
        } else {
            throw new DomainException('Not provide a correct table.');
        }
        return $table;
    }

    public function exists($tableName)
    {
        return $this->caller->behavior()->tableExists($tableName);
    }
}
