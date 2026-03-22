<?php

namespace PMVC\PlugIn\orm;

use PHPUnit\Framework\TestCase;
use PMVC\PlugIn\orm\Behaviors\BuildDropTable;
use PMVC\PlugIn\orm\Behaviors\BuildAddColumn;
use PMVC\PlugIn\orm\Behaviors\BuildDropColumn;
use PMVC\PlugIn\orm\Behaviors\BuildAlterColumn;
use PMVC\PlugIn\orm\Behaviors\BuildRenameColumn;
use PMVC\PlugIn\orm\Behaviors\BuildRenameTable;
use PMVC\PlugIn\orm\Behaviors\BuildCreateIndex;
use PMVC\PlugIn\orm\Behaviors\BuildDropIndex;
use PMVC\PlugIn\orm\Behaviors\BuildAddConstraint;
use PMVC\PlugIn\orm\Behaviors\BuildDropConstraint;

class BehaviorTest extends TestCase
{
    private $_engine;

    protected function setUp(): void
    {
        $this->_engine = new Engine();
    }

    // --- BuildDropTable ---

    public function testBuildDropTableAcceptReturnsItself()
    {
        $behavior = new BuildDropTable('users');
        $result = $behavior->accept($this->_engine);
        $this->assertInstanceOf(BuildDropTable::class, $result);
    }

    public function testBuildDropTableProcessGeneratesCorrectSql()
    {
        $behavior = new BuildDropTable('users');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('DROP TABLE IF EXISTS users', $sql);
    }

    public function testBuildDropTableWithDifferentTableName()
    {
        $behavior = new BuildDropTable('products');
        $behavior->accept($this->_engine);
        $this->assertEquals('DROP TABLE IF EXISTS products', $behavior->process());
    }

    // --- BuildAddColumn ---

    public function testBuildAddColumnBasic()
    {
        $behavior = new BuildAddColumn('users', 'email', 'varchar(255)');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE users ADD COLUMN email varchar(255)', $sql);
    }

    public function testBuildAddColumnWithNotNull()
    {
        $behavior = new BuildAddColumn('users', 'email', 'varchar(255)', ['notNull' => true]);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertStringContainsString('NOT NULL', $sql);
    }

    public function testBuildAddColumnWithDefault()
    {
        $behavior = new BuildAddColumn('users', 'status', 'integer', ['default' => '0']);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertStringContainsString('DEFAULT 0', $sql);
    }

    public function testBuildAddColumnWithUnique()
    {
        $behavior = new BuildAddColumn('users', 'email', 'varchar(255)', ['unique' => true]);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertStringContainsString('UNIQUE', $sql);
    }

    public function testBuildAddColumnWithAllOptions()
    {
        $behavior = new BuildAddColumn('users', 'code', 'varchar(10)', [
            'notNull' => true,
            'default' => "'active'",
            'unique' => true,
        ]);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertStringContainsString('NOT NULL', $sql);
        $this->assertStringContainsString("DEFAULT 'active'", $sql);
        $this->assertStringContainsString('UNIQUE', $sql);
    }

    // --- BuildDropColumn ---

    public function testBuildDropColumnBasic()
    {
        $behavior = new BuildDropColumn('users', 'email');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE users DROP COLUMN email', $sql);
    }

    public function testBuildDropColumnDifferentTable()
    {
        $behavior = new BuildDropColumn('products', 'price');
        $behavior->accept($this->_engine);
        $this->assertEquals('ALTER TABLE products DROP COLUMN price', $behavior->process());
    }

    // --- BuildAlterColumn ---

    public function testBuildAlterColumnTypeOnly()
    {
        $behavior = new BuildAlterColumn('users', 'age', 'bigint');
        $behavior->accept($this->_engine);
        $sqls = $behavior->process();
        $this->assertIsArray($sqls);
        $this->assertCount(1, $sqls);
        $this->assertEquals('ALTER TABLE users ALTER COLUMN age TYPE bigint', $sqls[0]);
    }

    public function testBuildAlterColumnWithNotNullSet()
    {
        $behavior = new BuildAlterColumn('users', 'email', 'varchar(255)', ['notNull' => true]);
        $behavior->accept($this->_engine);
        $sqls = $behavior->process();
        $this->assertCount(2, $sqls);
        $this->assertStringContainsString('SET NOT NULL', $sqls[1]);
    }

    public function testBuildAlterColumnWithNotNullDrop()
    {
        $behavior = new BuildAlterColumn('users', 'email', 'varchar(255)', ['notNull' => false]);
        $behavior->accept($this->_engine);
        $sqls = $behavior->process();
        $this->assertCount(2, $sqls);
        $this->assertStringContainsString('DROP NOT NULL', $sqls[1]);
    }

    public function testBuildAlterColumnWithDefaultSet()
    {
        $behavior = new BuildAlterColumn('users', 'status', 'integer', ['default' => '1']);
        $behavior->accept($this->_engine);
        $sqls = $behavior->process();
        $this->assertCount(2, $sqls);
        $this->assertStringContainsString('SET DEFAULT 1', $sqls[1]);
    }

    public function testBuildAlterColumnWithDefaultDrop()
    {
        $behavior = new BuildAlterColumn('users', 'status', 'integer', ['default' => null]);
        $behavior->accept($this->_engine);
        $sqls = $behavior->process();
        $this->assertCount(2, $sqls);
        $this->assertStringContainsString('DROP DEFAULT', $sqls[1]);
    }

    // --- BuildRenameColumn ---

    public function testBuildRenameColumnBasic()
    {
        $behavior = new BuildRenameColumn('users', 'name', 'full_name');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE users RENAME COLUMN name TO full_name', $sql);
    }

    // --- BuildRenameTable ---

    public function testBuildRenameTableBasic()
    {
        $behavior = new BuildRenameTable('users', 'accounts');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE users RENAME TO accounts', $sql);
    }

    // --- BuildCreateIndex ---

    public function testBuildCreateIndexBasic()
    {
        $behavior = new BuildCreateIndex('users', 'idx_users_email', ['email']);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('CREATE INDEX idx_users_email ON users (email)', $sql);
    }

    public function testBuildCreateIndexUnique()
    {
        $behavior = new BuildCreateIndex('users', 'idx_users_email', ['email'], true);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('CREATE UNIQUE INDEX idx_users_email ON users (email)', $sql);
    }

    public function testBuildCreateIndexMultipleColumns()
    {
        $behavior = new BuildCreateIndex('orders', 'idx_orders_user_date', ['user_id', 'created_at']);
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertStringContainsString('(user_id, created_at)', $sql);
    }

    // --- BuildDropIndex ---

    public function testBuildDropIndexBasic()
    {
        $behavior = new BuildDropIndex('idx_users_email');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('DROP INDEX IF EXISTS idx_users_email', $sql);
    }

    // --- BuildAddConstraint ---

    public function testBuildAddConstraintCheck()
    {
        $behavior = new BuildAddConstraint('products', 'chk_price', 'CHECK (price > 0)');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE products ADD CONSTRAINT chk_price CHECK (price > 0)', $sql);
    }

    public function testBuildAddConstraintUnique()
    {
        $behavior = new BuildAddConstraint('users', 'uq_email', 'UNIQUE (email)');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE users ADD CONSTRAINT uq_email UNIQUE (email)', $sql);
    }

    // --- BuildDropConstraint ---

    public function testBuildDropConstraintBasic()
    {
        $behavior = new BuildDropConstraint('products', 'chk_price');
        $behavior->accept($this->_engine);
        $sql = $behavior->process();
        $this->assertEquals('ALTER TABLE products DROP CONSTRAINT chk_price', $sql);
    }
}
