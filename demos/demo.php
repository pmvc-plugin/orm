<?php

include_once(__DIR__.'/../vendor/autoload.php');

\PMVC\Load::plug(null, [__DIR__.'/../../']);

\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug('orm');
$table = $orm->create_table('profile');
$table->column('id', 'int');
$table->column('id2', 'int');
$table['PRIMARY'] = ['id', 'id2'];

// var_dump((string)$table);

$pdo = $orm->pdo('sqlite:./test.sqlite');
$res = $pdo->exec((string)$table);
//$res = $pdo->getResult("SELECT * FROM sqlite_master WHERE type='table';");


// $res = $pdo->getResult("SELECT * FROM sqlite_schema WHERE type='table';");
$sql = <<<EOF
INSERT INTO "profile" ("id", "id2")
VALUES ('1', '1');
EOF;
//$pdo->exec($sql);
$res = $pdo->getAll("SELECT * FROM profile;");
var_dump($res);

// $res = $pdo->getResult("SELECT * FROM sqlite_schema;");
// var_dump($res);
