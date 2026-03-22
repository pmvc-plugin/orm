<?php
/**
 * CRUD Demo: Create
 *
 * Django equivalent:
 *   Product.objects.create(productId=1, productName='Laptop')
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

// Create table if not exists (like Django's migrate)
if (!$orm->remote()->exists('product')) {
    $orm->remote()->create($product)->commit()->process();
}

// Django: Product.objects.create(productId=1, productName='Laptop')
$r1 = $product->create(['productId' => 1, 'productName' => 'Laptop'])->process();

// Django: Product.objects.create(productId=2, productName='Mouse')
$r2 = $product->create(['productId' => 2, 'productName' => 'Mouse'])->process();

// Django: Product.objects.create(productId=3, productName='Keyboard')
$r3 = $product->create(['productId' => 3, 'productName' => 'Keyboard'])->process();

var_dump($r1, $r2, $r3);
