<?php
include_once(__DIR__.'/../vendor/autoload.php');

\PMVC\Load::plug(null, [__DIR__.'/../../']);
\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug("orm");
const migrationFolder = __DIR__.'/migrations'; 

$oSN = $orm->get_serial_number(migrationFolder);



var_dump($oSN->getNextName(), $oSN->getLastFile());

