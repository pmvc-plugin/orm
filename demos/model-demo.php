<?php
include_once(__DIR__.'/../vendor/autoload.php');

\PMVC\Load::plug(null, [__DIR__.'/../../']);
\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug('orm', [
  "databases" => [
    "default" => [
      "type" => 'sqlite',
      'file' => __DIR__. '/test.sqlite' 
    ]
  ]
]);
const migrationFolder = __DIR__.'/migrations'; 
//$res = $orm->schema()->diffFromModelToMigration(['../dev/models/'], migrationFolder);
//$res = $orm->schema()->fromMigrations([migrationFolder]);
$res = $orm->schema()->diffFromModelToMigration(__DIR__.'/models/', migrationFolder);
// var_dump($res);

//$orm->setEngine();
//$orm->migration(migrationFolder);
//$history = $orm->dao()->getDefault()->getQueue();
//\PMVC\d($history);
