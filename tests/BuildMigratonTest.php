<?php

namespace PMVC\PlugIn\orm;

use PHPUnit\Framework\TestCase;

class BuildMigratonTest extends TestCase
{
    private $_builder;

    protected function setUp(): void
    {
        $this->_builder = \PMVC\plug('orm')->build_migration();
    }

    public function testBuildDeleteModelGeneratesCode()
    {
        $code = $this->_builder->buildDeleteModel('users');
        $this->assertStringContainsString("\$dao->deleteModel('users')", $code);
        $this->assertStringContainsString('->process()', $code);
    }

    public function testBuildAddFieldGeneratesCode()
    {
        $field = ['name' => 'email', 'type' => 'CharField'];
        $code = $this->_builder->buildAddField('users', $field);
        $this->assertStringContainsString("\$dao->addField('users', 'email', 'CharField'", $code);
        $this->assertStringContainsString('->process()', $code);
    }

    public function testBuildAddFieldWithOptionsGeneratesCode()
    {
        $field = ['name' => 'status', 'type' => 'IntegerField', 'notNull' => true, 'default' => 0];
        $code = $this->_builder->buildAddField('products', $field);
        $this->assertStringContainsString("'notNull' => true", $code);
        $this->assertStringContainsString("'default' => 0", $code);
    }

    public function testBuildRemoveFieldGeneratesCode()
    {
        $code = $this->_builder->buildRemoveField('users', 'email');
        $this->assertStringContainsString("\$dao->removeField('users', 'email')", $code);
    }

    public function testBuildAlterFieldGeneratesCode()
    {
        $field = ['name' => 'age', 'type' => 'BigIntegerField'];
        $code = $this->_builder->buildAlterField('users', $field);
        $this->assertStringContainsString("\$dao->alterField('users', 'age', 'BigIntegerField'", $code);
    }

    public function testBuildRenameFieldGeneratesCode()
    {
        $code = $this->_builder->buildRenameField('users', 'name', 'full_name');
        $this->assertStringContainsString("\$dao->renameField('users', 'name', 'full_name')", $code);
    }

    public function testBuildRenameModelGeneratesCode()
    {
        $code = $this->_builder->buildRenameModel('users', 'accounts');
        $this->assertStringContainsString("\$dao->renameModel('users', 'accounts')", $code);
    }

    public function testBuildAddIndexGeneratesCode()
    {
        $code = $this->_builder->buildAddIndex('users', 'idx_email', ['email']);
        $this->assertStringContainsString("\$dao->addIndex('users', 'idx_email'", $code);
        $this->assertStringContainsString("['email']", $code);
    }

    public function testBuildAddUniqueIndexGeneratesCode()
    {
        $code = $this->_builder->buildAddIndex('users', 'idx_email', ['email'], true);
        $this->assertStringContainsString('true', $code);
    }

    public function testBuildRemoveIndexGeneratesCode()
    {
        $code = $this->_builder->buildRemoveIndex('idx_email');
        $this->assertStringContainsString("\$dao->removeIndex('idx_email')", $code);
    }
}
