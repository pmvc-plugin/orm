<?php
include_once __DIR__ . "/../vendor/autoload.php";
\PMVC\Load::plug(null, [__DIR__ . "/../../"]);
\PMVC\plug('dev')->debug_with_cli("debug");

$orm = \PMVC\plug("orm", [
  "databases" => [
    "default" => [
      "type" => "pgsql",
      "host" => "pgsql",
      "dbname" => "postgres",
      "user"=>"postgres",
      "password"=>"",
    ],
  ],
]);

$orm->setEngine();
$recorder = $orm->migration()->getRecorder();
// $result = $recorder->getVar('name')->process();

$result = $recorder->create([
  'prefix' => 'tp',
  'name' => 'tn',
  'applied' => date('Y-m-d H:i:s')  
])->process();


\PMVC\d($result);
