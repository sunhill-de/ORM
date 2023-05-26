<?php
/**
 * @file MysqlMigrateFresh
 * Helper that creates a fresh table of the given object or collection
 */
namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;

class MysqlMigrateFresh
{
    
    protected $class_name;
    
    protected $basic_table_name;
    
    public function __construct(public $storage) {}

    public function doMigrate()
    {
        $this->class_name = $this->storage->getCaller()::getInfo('name');
        $this->basic_table_name = $this->storage->getCaller()::getInfo('table');
        $this->createBasicTable();
    }
    
    protected function createBasicTable()
    {
        Schema::create($this->basic_table_name, function ($table) {
            $table->integer('id');
            $table->primary('id');
            $simple = $this->storage->getCaller()->getProperties();
            $helper = new MysqlObjectAddColumn($table);
            foreach ($simple as $field => $info) {
                $helper->handleProperty($info);
            }
        });            
    }
    
}