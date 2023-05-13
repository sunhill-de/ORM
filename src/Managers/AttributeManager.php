<?php
 
/**
 * @file AttributeManager.php
 * Provides the AttributeManager object for accessing information about attributes
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-08
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerTagTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\AttributeException;

/**
 * The AttributeManager is accessed via the Attributes facade. It's a singelton class
 */
class AttributeManager 
{

    /**
     * Returns all attributes from the database. If offset is set the entries start
     * with the $offset-th entry, if limit is set, the result entries are limited to 
     * that value.
     * @param int $offset
     * @param int $limit
     */
    public function getAllAttributes(int $offset = 0, int $limit = 0)
    {
        $query = DB::table('attributes');
        if ($offset) {
            $query = $query->offset($offset);
        }
        if ($limit) {
            $query = $query->limit($limit);
        }
        return $query->get();
    }
 
    /**
     * Returns the total count of attributes
     * @return int
     */
    public function getCount(): int
    {
        $result = DB::table('attributes')->count();
        return $result;
    }
    
    /**
     * Returns the attribute with the given id
     * @param int $id
     * @return array
     */
    public function getAttribute(int $id)
    {
        return DB::table('attributes')->where('id',$id)->first();        
    }

    /**
     * Adds a new attribute
     * @param string $name
     * @param string $type
     * @param string $allowed_objects
     * @param string $property
     */
    public function addAttribute(string $name, string $type, string $allowed_objects = 'object', string $property = '')
    {
        DB::table('attributes')->insert(
            [
                'name'=>$name,
                'type'=>$type,
                'allowedobjects'=>$allowed_objects,
                'property'=>$property                
            ]);
    }
    
    public function updateAttribute(int $id, string $name, string $type, string $allowed_objects = 'object', string $property = '')
    {
        DB::table('attributes')->where('id',$id)->update(
            [
                'name'=>$name,
                'type'=>$type,
                'allowedobjects'=>$allowed_objects,
                'property'=>$property
            ]);
    }
    
    protected function deleteDatabase(int $id)
    {
        DB::table('attributes')->where('id',$id)->delete();    
    }
    
    protected function deleteReferences(int $id)
    {
        DB::table('attributevalues')->where('attribute_id',$id)->delete();
    }
    
    public function deleteAttribute(int $id)
    {
        $this->deleteDatabase($id);
        $this->deleteReferences($id);
    }
    
    public function getAssociatedObjectsCount(int $id): int
    {
        return DB::table('attributevalues')->where('attribute_id',$id)->count();    
    }
    
    public function getAssociatedObjects(int $id, int $offset = 0, int $limit = 0)
    {
        $query = DB::table('attributevalues')->select('object_id as id')->where('attribute_id',$id);
        if ($offset) {
            $query = $query->offset($offset);
        }
        if ($limit) {
            $query = $query->limit($limit);
        }
        return $query->get();
    }
    
    protected function isAllowedClass($test, $allowed_classes)
    {
        $allowed = explode(',',$allowed_classes);
        foreach ($allowed as $class) {
            if (($class == 'object') || Classes::isA($test,$class)) {
                return true;
            }
        }
        return false;
    }
    
    public function getAvaiableAttributesForClass(string $class, array $without = [])
    {
        $result = [];
        $query = DB::table('attributes')->get();
        foreach ($query as $attribute) {
            if (!in_array($attribute->name,$without) && $this->isAllowedClass($class, $attribute->allowedobjects)) {
                $element = new \StdClass();
                $element->name = $attribute->name;
                $result[] = $attribute;                                    
            }
        }
        return $result;
    }
    
    public function getAttributeForClass(string $class, string $name): \StdClass
    {
        if (empty($query = DB::table('attributes')->where('name', $name)->first())) {
            throw new AttributeException("The attribute '$name' doesn't exist.");
        }
        if (!$this->isAllowedClass($class, $query->allowedobjects)) {
            throw new AttributeException("The attribute '$name' is not allowed for class '$class'.");
        }
        return $query;
    }
    
    public function getAttributeType(string $name): string
    {
        $query = DB::table('attributes')->select('type')->where('name',$name)->first();
        return $query->type;        
    }
}
 
