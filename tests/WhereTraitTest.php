<?php

namespace PMVC\PlugIn\orm;

use PHPUnit\Framework\TestCase;

class MockBindSql
{
    private $_bindData = [];
    private $_counter = 0;

    public function getBindName($v, $name = '')
    {
        $keyName = $name . '_' . $this->_counter++;
        $this->_bindData[$keyName] = $v;
        return ':' . $keyName;
    }

    public function getBindData()
    {
        return $this->_bindData;
    }
}

class WhereTestable
{
    use \PMVC\PlugIn\orm\crud\WhereTrait;

    public function testGetWhere($oSql): string
    {
        return $this->getWhere($oSql);
    }
}

class WhereTraitTest extends TestCase
{
    private function q(): WhereTestable { return new WhereTestable(); }
    private function s(): MockBindSql { return new MockBindSql(); }

    public function testAllLookupOperatorsGenerateSQL()
    {
        $cases = [
            ['exact',       'name', 'John',   'name = :name_'],
            ['iexact',      'name', 'John',   'LOWER(name) = :name_'],
            ['contains',    'title', 'hi',    'title LIKE :title_'],
            ['icontains',   'title', 'Hi',    'LOWER(title) LIKE :title_'],
            ['startswith',  'name', 'Jo',     'name LIKE :name_'],
            ['istartswith', 'name', 'Jo',     'LOWER(name) LIKE :name_'],
            ['endswith',    'email', '.com',   'email LIKE :email_'],
            ['iendswith',   'email', '.COM',   'LOWER(email) LIKE :email_'],
            ['gt',          'age', 18,         'age > :age_'],
            ['gte',         'age', 18,         'age >= :age_'],
            ['lt',          'price', 100,      'price < :price_'],
            ['lte',         'price', 100,      'price <= :price_'],
        ];
        foreach ($cases as [$op, $col, $val, $expected]) {
            $q = $this->q(); $s = $this->s();
            $q->$op($col, $val);
            $where = $q->testGetWhere($s);
            $this->assertStringStartsWith('WHERE ', $where, "Op '$op' missing WHERE prefix");
            $this->assertStringContainsString($expected, $where, "Op '$op' failed");
        }
    }

    public function testNewLookups()
    {
        // in
        $q = $this->q(); $s = $this->s();
        $q->in('status', ['a', 'b', 'c']);
        $this->assertStringContainsString('status IN (', $q->testGetWhere($s));
        $this->assertCount(3, $s->getBindData());

        // range
        $q = $this->q(); $s = $this->s();
        $q->range('price', 10, 100);
        $this->assertStringContainsString('BETWEEN', $q->testGetWhere($s));

        // isnull
        $q = $this->q(); $s = $this->s();
        $q->isnull('x');
        $this->assertStringContainsString('x IS NULL', $q->testGetWhere($s));

        $q = $this->q(); $s = $this->s();
        $q->isnull('x', false);
        $this->assertStringContainsString('x IS NOT NULL', $q->testGetWhere($s));
    }

    public function testDjangoStyleFilter()
    {
        // filter('field', value) → exact
        $q = $this->q(); $s = $this->s();
        $q->filter('status', 'active');
        $this->assertStringContainsString('status = :status_', $q->testGetWhere($s));

        // filter('field__lookup', value)
        $q = $this->q(); $s = $this->s();
        $q->filter('age__gt', 18);
        $this->assertStringContainsString('age > :age_', $q->testGetWhere($s));

        // backward compat: filter('or')
        $q = $this->q(); $s = $this->s();
        $q->filter('or')->exact('a', 1)->exact('b', 2);
        $this->assertStringContainsString(' OR ', $q->testGetWhere($s));
    }

    public function testExclude()
    {
        $q = $this->q(); $s = $this->s();
        $q->exclude('status', 'deleted');
        $this->assertStringContainsString('NOT (status = :status_', $q->testGetWhere($s));

        $q = $this->q(); $s = $this->s();
        $q->exclude('age__lt', 18);
        $this->assertStringContainsString('NOT (age < :age_', $q->testGetWhere($s));
    }

    public function testImplodeSpacing()
    {
        $q = $this->q(); $s = $this->s();
        $q->exact('a', 1)->exact('b', 2);
        $where = $q->testGetWhere($s);
        $this->assertStringContainsString(' AND ', $where);
        $this->assertDoesNotMatchRegularExpression('/:\w+AND/', $where);
    }

    public function testEmptyWhereReturnsEmpty()
    {
        $this->assertSame('', $this->q()->testGetWhere($this->s()));
    }

    public function testChainingReturnsThis()
    {
        $q = $this->q();
        $this->assertSame($q, $q->exact('a', 1));
        $this->assertSame($q, $q->filter('b', 2));
        $this->assertSame($q, $q->exclude('c', 3));
        $this->assertSame($q, $q->in('d', [4]));
        $this->assertSame($q, $q->isnull('e'));
    }

    public function testBindParamSecurity()
    {
        $q = $this->q(); $s = $this->s();
        $q->exact('a', "Robert'; DROP TABLE users;--");
        $where = $q->testGetWhere($s);
        $this->assertStringNotContainsString('DROP TABLE', $where);
        $this->assertStringContainsString(':a_', $where);
    }

    public function testInWithEmptyArray()
    {
        $q = $this->q(); $s = $this->s();
        $q->in('status', []);
        $where = $q->testGetWhere($s);
        // Empty IN → 1=0 (always false, valid SQL)
        $this->assertStringContainsString('1=0', $where);
        $this->assertStringNotContainsString('IN ()', $where);
    }

    public function testMultipleFilterCallsAccumulate()
    {
        $q = $this->q(); $s = $this->s();
        $q->filter('a', 1)->filter('b', 2)->filter('c__gt', 3);
        $where = $q->testGetWhere($s);
        $this->assertStringContainsString('a = :a_', $where);
        $this->assertStringContainsString('b = :b_', $where);
        $this->assertStringContainsString('c > :c_', $where);
        // All joined by AND
        $this->assertSame(2, substr_count($where, ' AND '));
    }

    public function testCaseInsensitiveBindValues()
    {
        // icontains must bind lowercased value with wildcards
        $q = $this->q(); $s = $this->s();
        $q->icontains('name', 'John');
        $q->testGetWhere($s);
        $bind = $s->getBindData();
        $this->assertContains('%john%', $bind);
        $this->assertNotContains('%John%', $bind);

        // iexact must bind lowercased value
        $q = $this->q(); $s = $this->s();
        $q->iexact('email', 'FOO@BAR.COM');
        $q->testGetWhere($s);
        $bind = $s->getBindData();
        $this->assertContains('foo@bar.com', $bind);
    }

    public function testExcludeWithOrMode()
    {
        $q = $this->q(); $s = $this->s();
        $q->filter('or');
        $q->filter('status', 'active');
        $q->exclude('role', 'admin');
        $where = $q->testGetWhere($s);
        $this->assertStringContainsString(' OR ', $where);
        $this->assertStringContainsString('NOT (role = :role_', $where);
    }

    public function testEngineRegexSqlDefault()
    {
        $engine = new Engine();
        $this->assertSame('col REGEXP :val', $engine->getRegexSql('col', ':val', false));
        $this->assertSame('LOWER(col) REGEXP LOWER(:val)', $engine->getRegexSql('col', ':val', true));
    }

    public function testPgsqlEngineRegexSql()
    {
        require_once __DIR__ . '/../src/_engine_pgsql.php';
        $engine = new PgsqlEngine();
        $this->assertSame('col ~ :val', $engine->getRegexSql('col', ':val', false));
        $this->assertSame('col ~* :val', $engine->getRegexSql('col', ':val', true));
    }
}
