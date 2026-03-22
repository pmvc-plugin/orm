<?php
/**
 * CRUD Demo: Update
 *
 * Django equivalent:
 *   Product.objects.filter(productId=1).update(productName='Laptop Pro')
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

// Django: Product.objects.filter(productId=1).update(productName='Laptop Pro')
$q = $product->update(['productName' => 'Laptop Pro']);
$q->exact('productId', 1);
$result = $q->process();
var_dump($result);

// Django: Product.objects.filter(productId=2).update(productName='Wireless Mouse')
$q = $product->update(['productName' => 'Wireless Mouse']);
$q->exact('productId', 2);
$result = $q->process();
var_dump($result);
