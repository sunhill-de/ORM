<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Storage\StorageAction;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionQuery;

class MysqlCollectionSearch extends StorageAction 
{
    
    public function run()
    {
        return new MysqlCollectionQuery($this->collection);
    }
    
}