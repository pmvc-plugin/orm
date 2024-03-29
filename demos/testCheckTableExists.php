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
\PMVC\d($orm->remote()->exists('aaa'));
