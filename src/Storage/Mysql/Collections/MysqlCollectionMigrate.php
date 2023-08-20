<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Storage\Mysql\Utils\TableManagement;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;
use Illuminate\Support\Facades\Schema;

class MysqlCollectionMigrate extends MysqlAction
{
    
    use PropertyHelpers, TableManagement;
        
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