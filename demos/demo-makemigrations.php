<?php
/**
 * Demo: makemigrations (Phase 2-3)
 *
 * Demonstrates auto-detection of model changes and migration generation.
 *
 * Django equivalent:
 *   python manage.py makemigrations
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

$modelDir = __DIR__ . '/models/';
$migrationDir = __DIR__ . '/migrations/';

echo "=== makemigrations (dry-run) ===\n";
$result = $orm->cli()->makemigrations($modelDir, $migrationDir, ['dry-run' => true]);
echo $result . "\n";

echo "\n=== makemigrations (write file) ===\n";
$result = $orm->cli()->makemigrations($modelDir, $migrationDir);
echo $result . "\n";
