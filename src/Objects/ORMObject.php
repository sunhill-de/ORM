<?php
/**
 * @file ORMObject.php
 * Provides the core object of the orm system named ORMObject
 * Lang en (complete)
 * Reviewstatus: 2023-05-09
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Storage\StorageMySQL;
use Sunhill\ORM\Properties\PropertyAttribute;
use Sunhill\ORM\Properties\AttributeException;
use Sunhill\ORM\Properties\PropertyTags;

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
class ORMObject extends PropertyCollection 
{
    const DEFAULT_OWNER=0;
    const DEFAULT_GROUP=0;
    const DEFAULT_READ=7;
    const DEFAULT_EDIT=7;
    const DEFAULT_DELETE=7;
    
    /**
     * Internal storage for queries that have to be executed later when this object has an id
     * @var array
     */
    protected $needid_queries = [];
    
    protected $loaded = false;
    
    protected $storageClass = 'Object';
    
 	protected function isLoaded(): bool
	{
	    return $this->loaded;
	}
		
// ============================ Storagefunctions  =======================================	
	/**
	 * Returns the current storage or creates one if it doesn't exist
     *
	 * @return StorageBase
	 */
	final protected function getStorage(): StorageBase 
    {
	    return Storage::createStorage($this);
	}
		    
// ================================ Loading ========================================	
	/**
	 * Checks, if the object with ID $id is in the cache. If yes, return it, othwerwise return false
	 * @param integer $id The id to search for
	 * @return bool|ORMObject false if not in cache otherwise the cache entry
	 */ 	 
	protected function checkCache(int $id)
    {
	    if (Objects::isCached($id)) {
	        return Objects::load($id);
	    }
	    return false;
	}
	
	/**
	 * Puts itself in the objects cache
	 * @param Int $id
	 */
	protected function insertCache(int $id) 
    {
	    Objects::insertCache($id,$this);	    
	}
	
	/**
	 * Loads the object from the storage
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\PropertyCollection::doLoad()
	 */
	protected function doLoad(StorageBase $storage) 
    {
	    if (!$this->isLoading()) {
	        $this->state = 'loading';
            $this->walkProperties('loadFromStorage',$storage);
            $this->loadAttributes($storage);
            $this->state = 'normal';
	    }
	}
	
	/**
	 * Read the attributes from the storage
	 */
	protected function loadAttributes(StorageBase $storage) 
    {
	    if (empty($storage->getEntity('attributes'))) {
	        return;
	    }
	    foreach ($storage->getEntity('attributes') as $value) {
	        if (!empty($value->property)) {
	            $property_name = $value->property;
	        } else {
	            $property_name = 'Attribute'.ucfirst($value->type);
	        }
	        $property = $this->dynamicAddProperty($value->name, $property_name);
	        $property->loadFromStorage($storage);	        
	    }
	}
	
	
// ========================= Insert =============================	
	/**
     * This method is called by the public method insert and 
     * inserts an object into the storage
	 * First for every property inserting is called, afterwards insert and finally inserted.
	 */
	protected function doInsert(StorageBase $storage, string $name)
    {
	       $storage = $this->getStorage();
           $this->walkProperties('insert',$storage);
           $this->setID($storage->insertObject());
           $this->insertCache($this->getID());
	}

// ========================== Update ===================================	
	protected function doUpdate(StorageBase $storage, string $name) 
    {
	    $storage = $this->getStorage();
	    $storage->setEntity('id',$this->getID());
	    $this->walkProperties('update',$storage);
	    $storage->updateObject($this->getID());
	}
		
	/**
	 * Creates a new empty storage
	 */
	public function createEmpty() 
    {
		
	}
	
	// ================================= Delete =============================================
	protected function doDelete() 
    {
	    $storage = $this->getStorage();
	    $this->walkProperties('delete',$storage);
	    $storage->deleteObject($this->getID());
	    $this->clearCacheEntry();
	}
	
    /**
     * Removes the entry from the cache
     */
	protected function clearCacheEntry() 
    {
	    Objects::clearCache($this->getID());
	}
	
	// ********************* Property handling *************************************	
	
	/**
	 * Recaculated all or one specific calculated fields. 
	 * If $property is set, only this one is recalculated
	 * @param unknown $property
	 */
	public function recalculate($property = null)
    {
	    if (!is_null($property)) {
	        $property_obj = $this->getProperty($property);
	        $property_obj->recalculate();
	    } else {
	        $properties = $this->getPropertiesWithFeature('calculated');
	        foreach ($properties as $property) {
	            $property->recalculate();
	        }	        
	    }
	}
	
// =============================== Copying ====================================	
	/**
	 * This routine copies the properties to $newobject
	 * @param ORMObject $newobject
	 */
	public function copyTo(ORMObject $newobject) 
    {
	    $newobject->setID($this->getID());
	    foreach ($this->properties as $property) {
	        $name = $property->getName();
	        switch ($property->getType()) {
	            case 'arrayOfObjects':
	            case 'arrayOfStrings':
	            case 'external_references':
	            case 'tags':
	                for ($i=0;$i<count($this->$name);$i++) {
	                    $newobject->$name[] = $this->$name[$i];
	                }
	                break;
	            case 'calculated':
	                break;
	            default:
	                $newobject->$name = $this->$name;
	        }
	    }
	}
	
	/**
	 * This routine copies the properties of the $source to this object
	 * @param ORMObject $source
	 */
	public function copyFrom(ORMObject $source) 
    {
	    $this->setID($source->getID());
	    foreach ($this->properties as $property) {
	        $name = $property->getName();
	        switch ($property->getType()) {
	            case 'arrayOfObjects':
	            case 'arrayOfStrings':
	            case 'external_references':
	            case 'tags':
	                for ($i=0;$i<count($source->$name);$i++) {
	                    $this->$name[] = $source->$name[$i];
	                }
	                break;
	            case 'calculated':
	                break;
	            default:
	                $this->$name = $source->$name;
	        }
	    }
	}
	
	/**
	 * This function just calls the routine of the Classes facade
	 * @param boolean $full
	 * @return unknown
	 */
	public function getInheritance(bool $full = false) 
    {
	    return Classes::getInheritanceOfClass(static::getInfo('name'), $full);
	}
		
	public function arrayFieldNewEntry($name,$index,$value) 
    {
	}
	
	public function arrayFieldRemovedEntry($name,$index,$value) 
    {
	}
	
	protected function handleUnknownProperty($name,$value) 
    {
	    if ($attribute = PropertyAttribute::search($name)) {
	        return $this->addAttribute($attribute,$value);
	    } else {
	        return parent::handleUnknownProperty($name, $value);
	    }
	}
	
	private function addAttribute($attribute,$value) 
    {
	   $this->checkAllowedClass($attribute);
	   // The attribute exists and may be used for this object
	   if (!empty($attribute->property)) {
	       $property_name = $attribute->property;
	   } else {
	       $property_name = 'Attribute'.ucfirst($attribute->type);
	   }
	   $property = $this->dynamicAddProperty($attribute->name, $property_name);
	   $property->setAllowedObjects($attribute->allowedobjects)
	   ->setAttributeName($attribute->name)
	   ->setAttributeType($attribute->type)
	   ->setAttributeProperty($attribute->property)
	   ->setAttributeID($attribute->id);
	   $property->setValue($value);
	   $property->setDirty(true);
	   return true;
	}
	
	private function checkAllowedClass($attribute) 
    {
	    $allowed_classes = explode(',',$attribute->allowedobjects);
	    if (!empty($allowed_classes)) {
	        $allowed = false;
	        foreach ($allowed_classes as $class) {
	            if (Classes::isA($this,$class)) {
	                $allowed = true;
	            }
	        }
	    }
	    if (!$allowed) {
	        throw new AttributeException("The attribute '".$attribute->name."' is not allowed for this object.");
	    }	    
	}
	
	// ********************** Static methods  ***************************	
	
	/**
	 * Initializes the properties of this object. Any child has to call its parents setupProperties() method
	 */
	protected static function setupProperties(PropertyList $list)
	{
	//    $list->addProperty(PropertyTags::class,'tags')->searchable();
	    $list->timestamp('created_at');
	    $list->timestamp('updated_at');
	    $list->varchar('uuid')->searchable()->setMaxLen(20);
	    $list->integer('obj_owner')->default(0);
	    $list->integer('obj_group')->default(0);
	    $list->integer('obj_read')->default(7);
	    $list->integer('obj_edit')->default(7);
	    $list->integer('obj_delete')->default(7);
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
	    static::addInfo('name_s', 'base object');
	    static::addInfo('name_p', 'base objects');
	    static::addInfo('description', 'A base class that defines storable properties.');
	    static::addInfo('options', 0);
	}
		
}
