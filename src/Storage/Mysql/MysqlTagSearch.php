<?php

namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Storage\StorageAction;
use Sunhill\ORM\Storage\Mysql\MysqlQuery;
use Sunhill\ORM\Storage\Mysql\MysqlTagQuery;

class MysqlTagSearch extends StorageAction 
{
    
    public function run()
    {
        return new MysqlTagQuery($this->collection);
    }
    
}