<?php
/**
 * @file ORMObject.php
 * Provides the core object of the orm system named ORMObject
 * Lang en (complete)
 * Reviewstatus: 2023-08-26
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 71.435 (2023-08-26)
 * Dependencies: Objects, ObjectException, base
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Facades\ObjectData;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyDatetime;

/**
 * As the central class of the ORM system ORMObject provides the basic function for
 * - loading and storing
 * - creating and erasing
 * - searching
 * Glossary:
 * - tags: A tag is an additional single word information that helps in grouping orm objects
 * - property: A property is a single entity of information of a single orm object (like an integer, string or date)
 * 
 * Policy:
 * - No direct database interaction. Should be handled by the storages
 * @author lokal
 */
class ORMObject extends PropertiesCollection 
{
    use HandlesStorage;
    
    const DEFAULT_OWNER=0;
    const DEFAULT_GROUP=0;
    const DEFAULT_READ=7;
    const DEFAULT_EDIT=7;
    const DEFAULT_DELETE=7;
        
    protected $loaded = false;
    
    protected $storageClass = 'Object';
    
 	protected function isLoaded(): bool
	{
	    return $this->loaded;
	}

	public function getIDName(): string
	{
	    return 'id';	    
	}
	
	public function getIDType(): string
	{
	    return 'int';
	}

// ================================ Loading ========================================	
	protected function prepareStore()
	{
	    $this->_uuid  = ObjectData::getUUID();
	    $this->_created_at = ObjectData::getDBTime();
	    $this->_updated_at = ObjectData::getDBTime();
	}
	
	protected function prepareUpdate()
	{
	    $this->_updated_at = ObjectData::getDBTime();
	}
		
	/**
	 * Creates a new empty storage
	 */
	public function createEmpty() 
    {
		
	}
		
	// ********************* Property handling *************************************		
    protected function searchAttribute($name)
    {
        $attributes = Attributes::getAvaiableAttributesForClass(static::class);
        foreach ($attributes as $attribute) {
            if ($attribute->name == $name) {
                return $attribute;
            }
        }
        return false;
    }
    
	protected function handleUnknownProperty($name,$value) 
    {
        if (!($attribute = $this->searchAttribute($name))) {
            return false;
        }
        $attribute_obj = $this->dynamicAddProperty($attribute->name, $attribute->type);
        $attribute_obj->set_AttributeID($attribute->id);
        $attribute_obj->loadValue($value);
        return true;
	}
	
	/**
	 * Makes this object to an object of a descendant class
	 * @param string $to_class The class that must be a direct or indirect descendant of the current class
	 * @param array $params additional params that are added to the new class
	 * @throws ClassIsSaneException is raised whenn $to_class is the current class
	 * @throws ClassNotARelativeException is raisen when $to_class is not a descendant of the current class
	 */
	public function promote(string $to_class, array $params)
	{
	    $storage = static::getStorage();
	    $storage->setCollection($this);
	    $storage->dispatch('promote', $to_class, $params);	    
	}
	
	/**
	 * Makes this object to an object of a ancestor class
	 * @param string $to_class The class that must be a direct or indirect ancestor of the current class
	 * @throws ClassIsSaneException is raised whenn $to_class is the current class
	 * @throws ClassNotARelativeException is raisen when $to_class is not an ancestor of the current class
	 */
	public function degrade(string $to_class)
	{
	    $storage = static::getStorage();
	    $storage->setCollection($this);
	    $storage->dispatch('degrade', $to_class);	    
	}
	
	// ********************** Static methods  ***************************	
	
	/**
	 * This function just calls the routine of the Classes facade
	 * @param boolean $full
	 * @return unknown
	 */
	public static function getInheritance(bool $full = false)
	{
	    return Classes::getInheritanceOfClass(static::getInfo('name'), $full);
	}
	
	protected static function getClassList()
	{
	    $list = static::getInheritance(true);
	    return array_map(function($entry) {
	        return Classes::getNamespaceOfClass($entry);
	    }, $list);
	}
	
	/**
	 * Initializes the properties of this object. Any child has to call its parents setupProperties() method
	 */
	protected static function setupProperties(PropertyList $list)
	{
	//    $list->addProperty(PropertyTags::class,'tags')->searchable();
	    $list->forceAddProperty(PropertyDatetime::class, '_created_at');
	    $list->forceAddProperty(PropertyDatetime::class, '_updated_at');
	    $list->forceAddProperty(PropertyVarchar::class, '_uuid')->setMaxLen(40)->default(null)->nullable();
	    $list->forceAddProperty(PropertyInteger::class, '_owner')->default(0);
	    $list->forceAddProperty(PropertyInteger::class, '_group')->default(0);
	    $list->forceAddProperty(PropertyInteger::class, '_read')->default(7);
	    $list->forceAddProperty(PropertyInteger::class, '_edit')->default(7);
	    $list->forceAddProperty(PropertyInteger::class, '_delete')->default(7);
	    
	    $list->tags();
	}

	/**
	 * This method must be overwritten by the derrived class to define its infos
	 * Test: /Unit/Objects/PropertyCollection_infoTest
	 */
	protected static function setupInfos()
	{
	    static::addInfo('name', 'object');
	    static::addInfo('table', 'objects');
	    static::addInfo('description', 'A base class that defines storable properties.');
	}
	
}
