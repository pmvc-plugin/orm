<?php
include_once(__DIR__.'/../vendor/autoload.php');

\PMVC\Load::plug(null, [__DIR__.'/../../']);
\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug('orm');
const migrationFolder = __DIR__.'/../dev/migrations'; 
// $res = $orm->schema()->diffFromModelToMigration(['../dev/models/Profile.php'], migrationFolder);
$orm->setEngine("sqlite");
$orm->migration(migrationFolder);

$history = $orm->dao()->getDefault()->getQueue();

\PMVC\d($history);
