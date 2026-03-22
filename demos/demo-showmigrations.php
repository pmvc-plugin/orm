<?php
/**
 * Demo: showmigrations (Phase 3)
 *
 * Displays migration status: [X] applied, [ ] pending.
 *
 * Django equivalent:
 *   python manage.py showmigrations
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

echo "=== showmigrations ===\n";
$result = $orm->cli()->showmigrations($migrationDir);
echo $result . "\n";
