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
use Illuminate\Support\Facades\Schema;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\AttributeException;
use Sunhill\ORM\Query\BasicQuery;

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
    public function addAttribute(string $name, string $type, string $allowed_objects = 'object')
    {
        DB::table('attributes')->insert(
            [
                'name'=>$name,
                'type'=>$type,
                'allowedobjects'=>$allowed_objects,
            ]);
    }
    
    public function updateAttribute(int $id, string $name, string $type, string $allowed_objects = 'object')
    {
        DB::table('attributes')->where('id',$id)->update(
            [
                'name'=>$name,
                'type'=>$type,
                'allowedobjects'=>$allowed_objects,
            ]);
    }
    
    protected function deleteDatabase(int $id)
    {
        DB::table('attributes')->where('id',$id)->delete();    
    }

    protected function getAttributeTableName(int $id): string
    {
        $values = DB::table('attributes')->where('id',$id)->first();
        return 'attr_'.$values->name;
    }
    
    protected function deleteReferences(int $id)
    {
        Schema::drop($this->getAttributeTableName($id));
    }
    
    public function deleteAttribute(int $id)
    {
        $this->deleteReferences($id);
        $this->deleteDatabase($id);
    }
    
    public function getAssociatedObjectsCount(int $id): int
    {
        return DB::table($this->getAttributeTableName($id))->count();    
    }
    
    public function getAssociatedObjects(int $id, int $offset = 0, int $limit = 0)
    {
        $query = DB::table($this->getAttributeTableName($id))->select('object_id as id');
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
    
    public function query(): BasicQuery
    {
        
    }
}
 
