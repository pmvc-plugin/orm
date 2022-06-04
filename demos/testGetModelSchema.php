<?php
include_once(__DIR__.'/../vendor/autoload.php');
\PMVC\Load::plug(null, [__DIR__.'/../../']);
\PMVC\plug('dev')->debug_with_cli("debug, trace");

$orm = \PMVC\plug("orm");

$modelFolder = __DIR__.'/models';
$modelSchema = $orm->schema()->fromModels($modelFolder);

\PMVC\d($modelSchema);
