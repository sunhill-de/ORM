<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;

/**
 * Helper class to erase an object from the database
 * @author klaus
 *
 */
class MysqlDelete
{
    
    use ClassTables;
    
    public function __construct(public $storage) {}
        
    
    public function doDelete(int $id)
    {
        $this->additional_tables = $this->collectAdditionalTables();
        
        $this->deleteObject($id);
        $this->deleteData($id);
        $this->deleteArrays($id);
        $this->deleteCalc($id);
        $this->deleteTags($id);
        $this->deleteAttributes($id);
    }
    
    protected function deleteObject($id)
    {
        DB::table('objects')->where('id',$id)->delete();
    }
    
    protected function deleteData($id)
    {
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy); // remove object
        
        foreach ($hirarchy as $class) {
            $table = Classes::getTableOfClass($class);
            DB::table($table)->where('id',$id)->delete();
        }
    }
    
    protected function deleteArrays($id)
    {
        foreach ($this->getArrayTables() as $table) {
            DB::table($table)->where('id',$id)->delete();
        }
    }
    
    protected function deleteCalc($id)
    {
        foreach ($this->getCalcTables() as $table) {
            DB::table($table)->where('id',$id)->delete();
        }        
    }
    
    protected function deleteTags($id)
    {
        DB::table('tagobjectassigns')->where('container_id',$id)->delete();    
    }
    
    protected function deleteAttributes($id)
    {
        DB::table('attributevalues')->where('object_id',$id)->delete();
    }
    
}