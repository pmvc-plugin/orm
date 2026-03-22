<?php
/**
 * Demo: DAO Operations (Phase 1)
 *
 * Demonstrates all 10 DAO schema operations:
 *   deleteModel, addField, removeField, alterField,
 *   renameField, renameModel, addIndex, removeIndex,
 *   addConstraint, removeConstraint
 *
 * Django equivalents:
 *   migrations.DeleteModel, migrations.AddField, etc.
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

// 1. Create a table first
echo "=== 1. Create Table ===\n";
$dao = $orm->dao()->getDefault();
$dao->createModel('demo_users')
    ->column('username', 'text')
    ->column('age', 'integer')
    ->commit()
    ->process();
echo "Created table: demo_users\n";

// 2. addField — ALTER TABLE ADD COLUMN
echo "\n=== 2. Add Field ===\n";
$dao->addField('demo_users', 'email', 'varchar(255)')->process();
echo "Added column: email\n";

// 3. addField with NOT NULL + DEFAULT
echo "\n=== 3. Add Field with constraints ===\n";
$dao->addField('demo_users', 'status', 'integer', [
    'notNull' => true,
    'default' => '1',
])->process();
echo "Added column: status (NOT NULL DEFAULT 1)\n";

// 4. renameField — RENAME COLUMN
echo "\n=== 4. Rename Field ===\n";
$dao->renameField('demo_users', 'username', 'full_name')->process();
echo "Renamed column: username → full_name\n";

// 5. alterField — ALTER COLUMN TYPE
echo "\n=== 5. Alter Field ===\n";
$dao->alterField('demo_users', 'age', 'bigint')->process();
echo "Altered column: age → bigint\n";

// 6. addIndex
echo "\n=== 6. Add Index ===\n";
$dao->addIndex('demo_users', 'idx_demo_users_email', ['email'])->process();
echo "Created index: idx_demo_users_email\n";

// 7. addIndex (UNIQUE)
echo "\n=== 7. Add Unique Index ===\n";
$dao->addIndex('demo_users', 'idx_demo_users_email_unique', ['email'], true)->process();
echo "Created unique index: idx_demo_users_email_unique\n";

// 8. removeIndex
echo "\n=== 8. Remove Index ===\n";
$dao->removeIndex('idx_demo_users_email')->process();
echo "Dropped index: idx_demo_users_email\n";

// 9. addConstraint
echo "\n=== 9. Add Constraint ===\n";
$dao->addConstraint('demo_users', 'chk_status', 'CHECK (status > 0)')->process();
echo "Added constraint: chk_status CHECK (status > 0)\n";

// 10. removeConstraint
echo "\n=== 10. Remove Constraint ===\n";
$dao->removeConstraint('demo_users', 'chk_status')->process();
echo "Dropped constraint: chk_status\n";

// 11. removeField
echo "\n=== 11. Remove Field ===\n";
$dao->removeField('demo_users', 'status')->process();
echo "Dropped column: status\n";

// 12. renameModel
echo "\n=== 12. Rename Model ===\n";
$dao->renameModel('demo_users', 'demo_accounts')->process();
echo "Renamed table: demo_users → demo_accounts\n";

// 13. deleteModel
echo "\n=== 13. Delete Model ===\n";
$dao->deleteModel('demo_accounts')->process();
echo "Dropped table: demo_accounts\n";

// 14. Show all queued SQL history
echo "\n=== SQL History ===\n";
foreach ($dao->getHistory() as $h) {
    echo "  " . print_r($h, true) . "\n";
}

echo "\nAll DAO operations completed successfully!\n";
