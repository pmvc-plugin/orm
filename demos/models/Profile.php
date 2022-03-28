<?php

use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Attrs\Field;
use PMVC\PlugIn\orm\BaseSqlModel;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\Profile';

#[Table("profile")] 
#[Field("id_a", "int")] 
#[Field("id-b", "int")] 
class Profile extends BaseSqlModel
{

}


