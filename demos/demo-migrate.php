<?php
/**
 * Demo: migrate (Phase 3-4)
 *
 * Applies pending migrations, skips already-applied ones.
 * Supports --fake and target migration.
 *
 * Django equivalent:
 *   python manage.py migrate
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

$migrationDir = __DIR__ . '/migrations/';

echo "=== migrate ===\n";
$result = $orm->cli()->migrate($migrationDir);
echo $result . "\n";

echo "\n=== showmigrations (after migrate) ===\n";
$result = $orm->cli()->showmigrations($migrationDir);
echo $result . "\n";

echo "\n=== migrate again (should skip applied) ===\n";
$result = $orm->cli()->migrate($migrationDir);
echo $result . "\n";
