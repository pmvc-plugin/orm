<?php

namespace PMVC\PlugIn\orm;

use PHPUnit\Framework\TestCase;

class StructureDAOTest extends TestCase
{
    private $_dao;

    protected function setUp(): void
    {
        $this->_dao = new StructureDAO();
    }

    public function testCreateModelAddsTable()
    {
        $table = $this->_dao->createModel('users');
        $this->assertNotNull($table);
        $arr = $this->_dao->toArray();
        $this->assertArrayHasKey('users', $arr);
    }

    public function testDeleteModelRemovesTable()
    {
        $this->_dao->createModel('users');
        $this->_dao->deleteModel('users');
        $arr = $this->_dao->toArray();
        $this->assertArrayNotHasKey('users', $arr);
    }

    public function testRenameModelChangesKey()
    {
        $this->_dao->createModel('users');
        $this->_dao->renameModel('users', 'accounts');
        $arr = $this->_dao->toArray();
        $this->assertArrayNotHasKey('users', $arr);
        $this->assertArrayHasKey('accounts', $arr);
    }

    public function testAddFieldAddsColumn()
    {
        $table = $this->_dao->createModel('users');
        $this->_dao->addField('users', 'email', 'CharField', ['MAX_LENGTH' => 255]);
        $columns = $table['TABLE_COLUMNS'];
        $this->assertArrayHasKey('email', \PMVC\toArray($columns));
    }

    public function testRemoveFieldRemovesColumn()
    {
        $table = $this->_dao->createModel('users');
        $table->column('email', 'CharField', ['MAX_LENGTH' => 255]);
        $this->_dao->removeField('users', 'email');
        $columns = $table['TABLE_COLUMNS'];
        $this->assertArrayNotHasKey('email', \PMVC\toArray($columns));
    }

    public function testRenameFieldChangesColumnKey()
    {
        $table = $this->_dao->createModel('users');
        $table->column('name', 'CharField', ['MAX_LENGTH' => 100]);
        $this->_dao->renameField('users', 'name', 'full_name');
        $columns = $table['TABLE_COLUMNS'];
        $colArray = \PMVC\toArray($columns);
        $this->assertArrayNotHasKey('name', $colArray);
        $this->assertArrayHasKey('full_name', $colArray);
    }

    public function testAlterFieldChangesType()
    {
        $table = $this->_dao->createModel('users');
        $table->column('age', 'int', []);
        $this->_dao->alterField('users', 'age', 'bigint');
        $columns = $table['TABLE_COLUMNS'];
        $colArray = \PMVC\toArray($columns);
        $this->assertEquals('bigint', $colArray['age']['type']);
    }

    public function testAlterFieldChangesOptions()
    {
        $table = $this->_dao->createModel('users');
        $table->column('email', 'text', []);
        $this->_dao->alterField('users', 'email', 'text', ['notNull' => true, 'default' => "'none'"]);
        $columns = $table['TABLE_COLUMNS'];
        $colArray = \PMVC\toArray($columns);
        $this->assertTrue($colArray['email']['notNull']);
        $this->assertEquals("'none'", $colArray['email']['default']);
    }

    public function testRenameFieldUpdatesNameKey()
    {
        $table = $this->_dao->createModel('users');
        $table->column('name', 'text', []);
        $this->_dao->renameField('users', 'name', 'full_name');
        $columns = $table['TABLE_COLUMNS'];
        $colArray = \PMVC\toArray($columns);
        $this->assertEquals('full_name', $colArray['full_name']['name']);
    }

    public function testCommitReturnsItself()
    {
        $result = $this->_dao->commit('any sql');
        $this->assertInstanceOf(StructureDAO::class, $result);
    }

    public function testProcessReturnsNull()
    {
        $result = $this->_dao->process();
        $this->assertNull($result);
    }

    public function testToArrayWhenEmpty()
    {
        $arr = $this->_dao->toArray();
        $this->assertIsArray($arr);
        $this->assertEmpty($arr);
    }
}
