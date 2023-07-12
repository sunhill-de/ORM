<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Sunhill\ORM\Storage\StorageAction;
use Sunhill\ORM\Storage\Mysql\MysqlQuery;

class MysqlCollectionSearch extends StorageAction 
{
    
    public function run()
    {
        return new MysqlQuery($this->collection);
    }
    
}