<?php
/**
 * Demo: SQL Generation via Visitor Pattern
 *
 * Shows how each Behavior class generates SQL through the Engine visitor.
 * This demo does NOT execute SQL — it only shows what would be generated.
 */

include_once __DIR__ . '/../vendor/autoload.php';
\PMVC\Load::plug(null, [__DIR__ . '/../../']);
\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug('orm', [
    'databases' => [
        'default' => [
            'type'     => 'pgsql',
            'host'     => 'pgsql',
            'dbname'   => 'postgres',
            'user'     => 'postgres',
            'password' => '',
        ],
    ],
]);
$orm->setEngine();

$behavior = $orm->behavior();

echo "=== SQL Generation via Visitor Pattern ===\n\n";

// DROP TABLE
$sql = $behavior->dropTable('users');
echo "dropTable:       {$sql}\n";

// ADD COLUMN
$sql = $behavior->addColumn('users', 'email', 'CharField', ['MAX_LENGTH' => 255]);
echo "addColumn:       {$sql}\n";

// ADD COLUMN with constraints
$sql = $behavior->addColumn('users', 'status', 'IntegerField', [
    'notNull' => true,
    'default' => '1',
]);
echo "addColumn+opts:  {$sql}\n";

// DROP COLUMN
$sql = $behavior->dropColumn('users', 'email');
echo "dropColumn:      {$sql}\n";

// ALTER COLUMN (returns array of SQL statements)
$sqls = $behavior->alterColumn('users', 'age', 'BigIntegerField', ['notNull' => true]);
echo "alterColumn:     " . join('; ', $sqls) . "\n";

// RENAME COLUMN
$sql = $behavior->renameColumn('users', 'name', 'full_name');
echo "renameColumn:    {$sql}\n";

// RENAME TABLE
$sql = $behavior->renameTable('users', 'accounts');
echo "renameTable:     {$sql}\n";

// CREATE INDEX
$sql = $behavior->createIndex('users', 'idx_email', ['email']);
echo "createIndex:     {$sql}\n";

// CREATE UNIQUE INDEX
$sql = $behavior->createIndex('users', 'idx_email_uq', ['email'], true);
echo "createUniqueIdx: {$sql}\n";

// DROP INDEX
$sql = $behavior->dropIndex('idx_email');
echo "dropIndex:       {$sql}\n";

// ADD CONSTRAINT
$sql = $behavior->addConstraint('orders', 'chk_total', 'CHECK (total > 0)');
echo "addConstraint:   {$sql}\n";

// DROP CONSTRAINT
$sql = $behavior->dropConstraint('orders', 'chk_total');
echo "dropConstraint:  {$sql}\n";

echo "\n=== PgSQL Engine Column Type Resolution ===\n";
$sql = $behavior->addColumn('products', 'price', 'DecimalField', [
    'MAX_DIGITS' => 10,
    'DECIMAL_PLACES' => 2,
]);
echo "DecimalField:    {$sql}\n";

$sql = $behavior->addColumn('products', 'metadata', 'JSONField');
echo "JSONField:       {$sql}\n";

$sql = $behavior->addColumn('products', 'created', 'DateTimeField');
echo "DateTimeField:   {$sql}\n";

echo "\nDone.\n";
