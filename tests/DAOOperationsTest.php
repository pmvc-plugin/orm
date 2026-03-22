<?php

namespace PMVC\PlugIn\orm;

use PHPUnit\Framework\TestCase;

class DAOOperationsTest extends TestCase
{
    private $_dao;
    private $_pOrm;

    protected function setUp(): void
    {
        $this->_pOrm = \PMVC\plug('orm');
        $this->_pOrm->setEngine();
        $this->_dao = new DAO();
    }

    public function testDeleteModelReturnsSelf()
    {
        $result = $this->_dao->deleteModel('users');
        $this->assertInstanceOf(DAO::class, $result);
    }

    public function testDeleteModelQueuesSql()
    {
        $this->_dao->deleteModel('users');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('DROP TABLE IF EXISTS users', $queue[0][0]);
    }

    public function testRenameModelQueuesSql()
    {
        $this->_dao->renameModel('users', 'accounts');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('ALTER TABLE users RENAME TO accounts', $queue[0][0]);
    }

    public function testAddFieldQueuesSql()
    {
        $this->_dao->addField('users', 'email', 'varchar(255)');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('ALTER TABLE users ADD COLUMN email', $queue[0][0]);
    }

    public function testAddFieldWithOptionsQueuesSql()
    {
        $this->_dao->addField('users', 'status', 'integer', ['notNull' => true, 'default' => '0']);
        $queue = $this->_dao->getQueue();
        $sql = $queue[0][0];
        $this->assertStringContainsString('NOT NULL', $sql);
        $this->assertStringContainsString('DEFAULT 0', $sql);
    }

    public function testRemoveFieldQueuesSql()
    {
        $this->_dao->removeField('users', 'email');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('ALTER TABLE users DROP COLUMN email', $queue[0][0]);
    }

    public function testAlterFieldQueuesSql()
    {
        $this->_dao->alterField('users', 'age', 'bigint');
        $queue = $this->_dao->getQueue();
        $this->assertGreaterThanOrEqual(1, count($queue));
        $this->assertStringContainsString('ALTER TABLE users ALTER COLUMN age TYPE bigint', $queue[0][0]);
    }

    public function testAlterFieldWithNotNullQueuesSql()
    {
        $this->_dao->alterField('users', 'email', 'varchar(255)', ['notNull' => true]);
        $queue = $this->_dao->getQueue();
        $this->assertGreaterThanOrEqual(2, count($queue));
    }

    public function testRenameFieldQueuesSql()
    {
        $this->_dao->renameField('users', 'name', 'full_name');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('ALTER TABLE users RENAME COLUMN name TO full_name', $queue[0][0]);
    }

    public function testAddIndexQueuesSql()
    {
        $this->_dao->addIndex('users', 'idx_email', ['email']);
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('CREATE INDEX idx_email ON users (email)', $queue[0][0]);
    }

    public function testAddUniqueIndexQueuesSql()
    {
        $this->_dao->addIndex('users', 'idx_email', ['email'], true);
        $queue = $this->_dao->getQueue();
        $this->assertStringContainsString('CREATE UNIQUE INDEX', $queue[0][0]);
    }

    public function testRemoveIndexQueuesSql()
    {
        $this->_dao->removeIndex('idx_email');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('DROP INDEX IF EXISTS idx_email', $queue[0][0]);
    }

    public function testAddConstraintQueuesSql()
    {
        $this->_dao->addConstraint('products', 'chk_price', 'CHECK (price > 0)');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('ADD CONSTRAINT chk_price CHECK (price > 0)', $queue[0][0]);
    }

    public function testRemoveConstraintQueuesSql()
    {
        $this->_dao->removeConstraint('products', 'chk_price');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('DROP CONSTRAINT chk_price', $queue[0][0]);
    }

    public function testRunSqlQueuesSql()
    {
        $this->_dao->runSql('SELECT 1');
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertEquals('SELECT 1', $queue[0][0]);
    }

    public function testChainingMultipleOperations()
    {
        $this->_dao
            ->addField('users', 'email', 'varchar(255)')
            ->addField('users', 'phone', 'varchar(20)')
            ->addIndex('users', 'idx_email', ['email']);
        $queue = $this->_dao->getQueue();
        $this->assertCount(3, $queue);
    }

    public function testAlterUniqueTogetherQueuesSql()
    {
        $this->_dao->alterUniqueTogether('users', ['email', 'phone']);
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('UNIQUE', $queue[0][0]);
    }

    public function testAlterIndexTogetherQueuesSql()
    {
        $this->_dao->alterIndexTogether('users', ['first_name', 'last_name']);
        $queue = $this->_dao->getQueue();
        $this->assertCount(1, $queue);
        $this->assertStringContainsString('CREATE INDEX', $queue[0][0]);
    }
}
