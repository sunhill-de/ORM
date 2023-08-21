<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionMigrateFresh;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionMigrateAlter;

class MysqlObjectMigrate extends MysqlAction
{
    
    public function run()
    {
        $table = ($this->collection)::getInfo('table');
        if (Schema::hasTable(($this->collection)::getInfo('table'))) {
            $migrator = new MysqlCollectionMigrateAlter($this->collection);
        } else {
            $migrator = new MysqlCollectionMigrateFresh($this->collection);
        }
        $migrator->setCollection($this->collection);
        $migrator->run();
    }
        
}