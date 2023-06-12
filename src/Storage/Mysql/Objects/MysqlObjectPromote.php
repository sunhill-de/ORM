<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;

class MysqlPromote
{
    
    use ClassTables;
    
    public function __construct(public $storage) {}

    protected $id = 0;
    
    public function doPromote()
    {
        $this->additional_tables = $this->collectAdditionalTables();
    }

    
}