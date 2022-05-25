<?php
include_once __DIR__ . "/../vendor/autoload.php";
\PMVC\Load::plug(null, [__DIR__ . "/../../"]);

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
