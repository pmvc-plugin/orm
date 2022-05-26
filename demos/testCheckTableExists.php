<?php
include_once __DIR__ . "/../vendor/autoload.php";
\PMVC\Load::plug(null, [__DIR__ . "/../../"]);
\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug("orm", [
  "databases" => [
    "default" => [
      "type" => "pgsql",
      "host" => "pgsql",
      "dbname" => "postgres",
      "user"=>"postgres",
      "password"=>"postgres",
    ],
  ],
]);

$orm->setEngine();
$orm->check_table_exists();
