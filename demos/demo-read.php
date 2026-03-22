<?php
/**
 * CRUD Demo: Read
 *
 * Django equivalents:
 *   Product.objects.all()
 *   Product.objects.get(productId=1)
 *   Product.objects.values_list('productName', flat=True).first()
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

// Django: Product.objects.all()
echo "--- getAll ---\n";
$all = $product->getAll()->process();
var_dump($all);

// Django: Product.objects.first()
echo "--- getOne ---\n";
$one = $product->getOne()->process();
var_dump($one);

// Django: Product.objects.values_list('productName', flat=True).first()
echo "--- getVar ---\n";
$name = $product->getVar('productname')->process();
var_dump($name);
