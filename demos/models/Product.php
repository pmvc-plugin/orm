<?php

use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Attrs\Field;
use PMVC\PlugIn\orm\BaseSqlModel;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Product';

#[Table("product")] 
#[Field("productId", "int")] 
#[Field("productName", "text")] 
class Product extends BaseSqlModel
{

}


