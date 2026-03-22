<?php
/**
 * CRUD Demo: Delete
 *
 * Django equivalent:
 *   Product.objects.filter(productId=3).delete()
 */

include_once(__DIR__.'/../vendor/autoload.php');
\PMVC\Load::plug(null, [__DIR__.'/../../']);
\PMVC\plug('dev')->debug_with_cli();

$orm = \PMVC\plug('orm', [
    'databases' => [
        'default' => [
            'type'     => 'pgsql',
            'host'     => 'pgsql',
            'dbname'   => 'postgres',
            'user'     => 'postgres',
            'password' => '',
        ],
    ],
]);
$orm->setEngine();

$productClass = \PMVC\importClass(\PMVC\l(__DIR__.'/models/Product.php'));
$product = new $productClass();

// Django: Product.objects.filter(productId=3).delete()
$q = $product->delete(null);
$q->exact('productId', 3);
$result = $q->process();
var_dump($result);
