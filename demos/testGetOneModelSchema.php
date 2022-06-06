<?php
include_once(__DIR__.'/../vendor/autoload.php');
\PMVC\Load::plug(null, [__DIR__.'/../../']);
\PMVC\plug('dev')->debug_with_cli("debug, trace");

$orm = \PMVC\plug("orm");

$model = __DIR__.'/models/Product';

$o = \PMVC\l($model);

$oModel = new (\PMVC\importClass($o));

\PMVC\d($oModel->getTableName());
