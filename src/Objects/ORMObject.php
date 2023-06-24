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

use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Search\QueryBuilder;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Objects\StorageInteraction\StorageInteractionBase;
use Sunhill\ORM\Objects\StorageInteraction\ObjectLoader;
use Sunhill\ORM\Objects\StorageInteraction\ObjectStorer;
use Sunhill\ORM\Objects\StorageInteraction\ObjectUpdater;

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

	protected function copyAttributes(StorageBase $storage)
	{
	    $result = [];
	    foreach ($this->dynamic_properties as $key => $property) {
	        $entry = new \StdClass();
	        $entry->name = $key;
	        $entry->attribute_id = $property->getAttributeID();
	        $entry->value = $property->getValue();
	        switch ($property::class) {
	            case  PropertyInteger::class:
	              $entry->type = 'integer'; break;
	            case  PropertyVarchar::class:
	                $entry->type = 'varchar'; break;
	            case  PropertyFloat::class:
	                $entry->type = 'float'; break;
	            case  PropertyBoolean::class:
	                $entry->type = 'boolean'; break;
	            case  PropertyText::class:
	                $entry->type = 'text'; break;
	            case  PropertyDate::class:
	                $entry->type = 'date'; break;
	            case  PropertyDatetime::class:
	                $entry->type = 'datetime'; break;
	            case  PropertyTime::class:
	                $entry->type = 'time'; break;
	        }
	        $result[] = $entry;
	    }
	    $storage->setEntity('attributes', $result);
	}
	
	protected function preCreation(StorageBase $storage)
	{
	    $this->copyAttributes($storage);
	}
	
	/**
	 * Is called after the creation of an object. In this case copy the created values
	 * uuid, created_at and updated_at back to the storage
	 * @param StorageBase $storage
	 */
	protected function postCreation(StorageBase $storage)
	{
	    $this->uuid = $storage->getEntity('uuid');
	    $this->created_at = $storage->getEntity('created_at');
	    $this->updated_at = $storage->getEntity('updated_at');
	}

	protected function preUpdate(StorageBase $storage)
	{
	    
	}
	
	protected function postUpdate(StorageBase $storage)
	{
	    
	}
	
	
// ============================ Storagefunctions  =======================================	

	public static function search() {
	    $query = new QueryBuilder();
	    $query->setCallingClass(get_called_class());
	    return $query;
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
	
	protected function loadTag(int $tag_id)
	{
	    $this->tags[] = Tags::loadTag($tag_id);
	}
	
	protected function loadTags(StorageBase $storage)
	{
	    if ($storage->hasEntity('tags')) {
	        foreach ($storage->getEntity('tags')->getValue() as $tag) {
	            $this->loadTag($tag);
	        }
	    }
	}
	
	protected function loadAttribute(\Stdclass $attribute)
	{
	    $property = $this->dynamicAddProperty($attribute->name, $attribute->type);
	    $property->setValue($attribute->value);	    
	}
	
	protected function loadAttributes(StorageBase $storage)
	{
	    if ($storage->hasEntity('attributes')) {
	        foreach ($storage->getEntity('attributes') as $attribute) {
	            $this->loadAttribute($attribute);
	        }
	    }
	}
	
	protected function loadAdditional(StorageBase $storage)
	{
	    $this->loadTags($storage);
	    $this->loadAttributes($storage);
	}
		
// ========================= Insert =============================	
	/**
     * This method is called by the public method insert and 
     * inserts an object into the storage
	 * First for every property inserting is called, afterwards insert and finally inserted.
	 */
	protected function doInsert(StorageBase $storage, string $name)
    {
	       $storage = static::getStorage();
           $this->walkProperties('insert',$storage);
           $this->setID($storage->insertObject());
           $this->insertCache($this->getID());
	}

// ========================== Update ===================================	
	protected function doUpdate(StorageBase $storage, string $name) 
    {
	    $storage = static::getStorage();
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
	    $storage = static::getStorage();
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
	        switch ($property::getType()) {
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
	        switch ($property::getType()) {
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
	
		
	protected function handleUnknownProperty($name,$value) 
    {
        $attribute = Attributes::getAttributeForClass(static::class, $name);
        $attribute_obj = $this->dynamicAddProperty($attribute->name, $attribute->type);
        $attribute_obj->setAttributeID($attribute->id);
        $attribute_obj->setValue($value);
        return true;
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
	    $list->forceAddProperty(PropertyVarchar::class, '_uuid')->setMaxLen(20)->default(null)->nullable();
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

	protected function getLoaderInteraction(): StorageInteractionBase
	{
	    return new ObjectLoader();
	}
	
	protected function getStorerInteraction(): StorageInteractionBase
	{
	    return new ObjectStorer();
	}
	
	protected function getUpdaterInteraction(): StorageInteractionBase
	{
	    return new ObjectUpdater();
	}
	
}
