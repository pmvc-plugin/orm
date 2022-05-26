<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\PDOWrap';

use PDO;
use DomainException;
use UnexpectedValueException;
use PDOException;
use PMVC\PlugIn\orm\DAO;

class PDOWrap
{
    private $_pdo;
    private $_dsn;
    private $_lastState;

    public function __invoke($dsn = null)
    {
        if (!is_null($dsn)) {
            $this->_initPdo($dsn);
        }
        return $this;
    }

    private function _initPdo($dsn)
    {
        $this->_dsn = $dsn;
        try {
            $this->_pdo = new PDO($dsn);
        } catch (PDOException $e) {
            $message = json_encode([
                'Get pdo failed.',
                'debug' => [
                    'your-dsn' => $dsn,
                    'orig-error' => $e->getMessage(),
                ],
            ]);
            throw new UnexpectedValueException($message);
        }
        return $this->_pdo;
    }

    /**
     * @see function _initPdo
     */
    private function _getPdo()
    {
        if (!$this->_pdo) {
            throw new DomainException('Not init pdo.');
        }
        return $this->_pdo;
    }

    public function exec($sql, array $bindParams = [])
    {
        $this->_lastState = $this->_getPdo()->prepare($sql);
        $result = $this->couldRun($this->_lastState)->execute($bindParams);
        return $result;
    }

    public function couldRun($o)
    {
        if (!$o) {
            throw new DomainException('PDOStatement not ready to run.');
        }
        return $o;
    }

    public function processDao(DAO $dao)
    {
        $queueArr = $dao->getQueue();
        $resultArr = [];
        foreach ($queueArr as $queue) {
            $resultArr[] = $this->exec($queue[0], $queue[1]);
        }
        return $resultArr;
    }

    public function getOne($sql, array $bindParams = [])
    {
        $arr = $this->geetAll($sql, $bindParams);
        return \PMVC\get($arr, '0');
    }

    public function getAll($sql, array $bindParams = [])
    {
        $isSuccess = $this->exec($sql, $bindParams);
        if ($isSuccess) {
            // https://www.php.net/manual/en/pdostatement.fetchall.php
            $result = $this->_lastState->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } else {
            return false;
        }
    }
}
