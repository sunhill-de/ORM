<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;

class MysqlMigrate
{
    
    public function __construct(public $storage) {}

    public function doMigrate()
    {
        if (Schema::hasTable($this->storage->getCaller()::getInfo('table'))) {
            $migrator = new MysqlMigrateAlter($this->storage);
        } else {
            $migrator = new MysqlMigrateFresh($this->storage);
        }
        $migrator->doMigrate();
    }
        
}