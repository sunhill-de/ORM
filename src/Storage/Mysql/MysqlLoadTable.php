<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;

/**
 * Helper class to load an object out of the database
 * @author klaus
 *
 */
class MysqlLoadTable
{
    
    use ClassTables;
    
    public function __construct(public $storage) {}
        
    
    public function doLoad(int $id)
    {
    }
            
}