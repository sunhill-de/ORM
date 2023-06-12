<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;

class MysqlObjectMigrate
{
    
    public function __construct(public $storage) {}

    public function doMigrate()
    {
        if (Schema::hasTable($this->storage->getCaller()::getInfo('table'))) {
            $migrator = new MysqlObjectMigrateAlter($this->storage);
        } else {
            $migrator = new MysqlObjectMigrateFresh($this->storage);
        }
        $migrator->doMigrate();
    }
        
}