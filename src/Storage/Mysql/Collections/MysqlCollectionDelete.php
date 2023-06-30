<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Storage\Exceptions\IDNotSetException;

class MysqlCollectionDelete extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers;
    
    public function run()
    {
        $this->handleID();
        $list = $this->collectClasses();
        $this->deleteClassTables($list);
        $this->runProperties();
    }
    
    protected function handleID()
    {
        if (!is_null($this->additional)) {
            return;
        }
        $id = $this->collection->getID();
        if (!empty($id)) {
            $this->additional = $id;
            return;
        }
        throw new IDNotSetException("There is no ID set for delete");
    }
    
    protected function deleteClassTables($list)
    {
        foreach ($list as $class) {
            $table = $class::getInfo('table');
            DB::table($table)->where('id', $this->additional)->delete();
        }
    }
    
    protected function handleArrayOrMap($property)
    {
        DB::table($this->getSpecialTableName($property))->where('id',$this->additional)->delete();
    }
    
    public function handlePropertyArray($property)
    {
        $this->handleArrayOrMap($property);
    }
    
    public function handlePropertyBoolean($property)
    {
    }
    
    public function handlePropertyCalculated($property)
    {
    }
    
    public function handlePropertyCollection($property)
    {
    }
    
    public function handlePropertyDate($property)
    {
    }
    
    public function handlePropertyDateTime($property)
    {
    }
    
    public function handlePropertyEnum($property)
    {
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyFloat($property)
    {
    }
    
    public function handlePropertyInformation($property)
    {
        
    }
    
    public function handlePropertyInteger($property)
    {
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($property)
    {
        $this->handleArrayOrMap($property);
    }
    
    public function handlePropertyObject($property)
    {
    }
    
    public function handlePropertyTags($property)
    {
    }
    
    public function handlePropertyText($property)
    {
    }
    
    public function handlePropertyTime($property)
    {
    }
    
    public function handlePropertyVarchar($property)
    {
    }
}