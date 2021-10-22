<?php
/**
 * @file ORMObject.php
 * Provides the core object of the orm system named ORMObject
 * Lang en (complete)
 * Reviewstatus: 2021-04-07
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 */
namespace Sunhill\ORM\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\PropertiesHaving;
use Sunhill\ORM\Objects\ObjectException;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Storage\StorageMySQL;
use Sunhill\ORM\Properties\PropertyAttribute;
use \Sunhill\ORM\Properties\AttributeException;

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
class ORMObject extends PropertiesHaving 
{

    /**
     * Static variable that stores the name of the database table.
     * @todo This should be moved to the storages in a later step
     * @var string
     */
    public static $table_name = 'objects';
    
    public static $object_infos = [
        'name'=>'object',       // A repetition of static:$object_name @todo see above
        'table'=>'objects',     // A repitition of static:$table_name
        'name_s'=>'object',     // A human readable name in singular
        'name_p'=>'objects',    // A human readable name in plural
        'description'=>'Baseclass of all other classes in the ORM system. An ORMObject should\'t be initiated directly',
        'options'=>0,           // Reserved for later purposes
    ];
    
    /**
     * Internal storage for queries that have to be executed later when this object has an id
     * @var array
     */
    protected $needid_queries = [];
    
    /**
     * Constructor for all orm classes. As a child of properties_having it calls its derrived constructor wich in turn initializes the properties.
     * Additionally it defines a few own intenal properties (tags and externalhooks)
     */
	public function __construct() 
    {
	    parent::__construct();
	    $this->properties['tags'] = self::createProperty('Tags','Tags','object')->setOwner($this);
	    $this->properties['externalhooks'] = self::createProperty('ExternalHooks','ExternalHooks','object')->setOwner($this);
	}
	
	// ========================================== NeedID-Queries ========================================
	
	
	/**
	 * Adds another entry to the needid_queries array. This array is needed for queries that have
	 * been executed before the id of the master object was ready. So this queries have to be updated
	 * with the actual ID. 
	 * @param string $table
	 * @param array $fixed
	 * @param string $id_field
	 */
	public function addNeedIDQuery(string $table, array $fixed, string $id_field) 
    {
	    $this->needid_queries[] = ['table'=>$table,'fixed'=>$fixed,'id_field'=>$id_field];
	}
	
	/**
	 * Processes all entries in the need_id_query
	 * @param StorageBase $storage
	 */
	protected function executeNeedIDQueries(StorageBase $storage) 
    {
	    $storage->entities['needid_queries'] = $this->needid_queries;
	    $storage->executeNeedIDQueries();
	}
	
// ============================ Storagefunktionen =======================================	
	/**
	 * Returns the current storage or creates one if it doesn't exist
	 * @return StorageBase
	 */
	final protected function getStorage(): StorageBase 
    {
	    return $this->createStorage();
	}
	
	/**
	 * Creates a storage. By default this is a mysql storage. This method could be overwritten for debug purposes
	 * @return StorageMySQL
	 */
	protected function createStorage(): StorageMySQL
    {
	    return new StorageMySQL($this);
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
	 * @see \Sunhill\ORM\PropertiesHaving::doLoad()
	 */
	protected function doLoad() 
    {
	    if (!$this->isLoading()) {
	        $this->state = 'loading';
	        $loader = $this->getStorage();
	        $this->walkProperties('loading',$loader);
	        $loader->loadObject($this->getID());
            $this->walkProperties('load',$loader);
            $this->loadAttributes($loader);
            $this->loadExternalHooks($loader);
            $this->walkProperties('loaded', $loader);
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
	    foreach ($storage->getEntity('attributes') as $name => $value) {
	        if (!empty($value['property'])) {
	            $property_name = $value['property'];
	        } else {
	            $property_name = 'Attribute'.ucfirst($value['type']);
	        }
	        $property = $this->dynamicAddProperty($name, $property_name);
	        $property->load($storage);	        
	    }
	}
	
	/**
	 * Reads the external hook from the storage
	 */
	protected function loadExternalHooks(StorageBase $storage)
    {
	}
	
// ========================= Insert =============================	
	/**
     * This method is called by the public method insert and 
     * inserts an object into the storage
	 * First for every property inserting is called, afterwards insert and finally inserted.
	 */
	protected function doInsert()
    {
	       $storage = $this->getStorage();
    	   $this->walkProperties('inserting', $storage);
           $this->walkProperties('insert',$storage);
           $this->setID($storage->insertObject());
           $this->executeNeedIDQueries($storage);
           $this->walkProperties('inserted',$storage);
           $this->insertCache($this->getID());
	}

// ========================== Update ===================================	
	protected function doUpdate() 
    {
	    $storage = $this->getStorage();
	    $storage->setEntity('id',$this->getID());
	    $this->walkProperties('updating', $storage);
	    $this->walkProperties('update',$storage);
	    $storage->updateObject($this->getID());
	    $this->walkProperties('updated',$storage);
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
	    $this->walkProperties('deleting',$storage);
	    $this->walkProperties('delete',$storage);
	    $storage->deleteObject($this->getID());
	    $this->walkProperties('deleted',$storage);
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
	
	/**
	 * A helper method that calls for every propery the given method an passes the given storage to it
	 * @param string $action The name of the method that has to get called
	 * @param \Sunhill\ORM\Storage\StorageBase $storage the storage
	 */
	protected function walkProperties(string $action, StorageBase $storage)
    {
	    $properties = $this->getPropertiesWithFeature();
	    foreach ($properties as $property) {
	        $property->$action($storage);
	    }
	}

// ================================== Promotion ===========================================		
	/**
	 * Raises this object to a (higher) class
	 * @param String $newclass
	 * @return unknown
	 */
	public function promote(string $newclass): ORMObject 
    {
        return Objects::promoteObject($this,$newclass);    
	}
	
	/**
	 * The old (lower) object is called before the promotion takes place.
	 * @param string $newclass
	 */
	public function prePromotion(string $newclass) 
    {
	    // Does nothing
	}
	
	/**
	 * The newly created (promoted) object is called after the promotion took place
	 * @param ORMObject $from The old (lower) object
	 */
	public function postPromotion(ORMObject $from) 
    {
	    // Does nothing
	}

// ===================================== Degration =============================================	
	public function degrade(String $newclass): ORMObject 
    {
	    return Objects::degradeObject($this,$newclass);
	}
	
	public function preDegration(string $newclass)
    {
	    
	}
	
	public function postDegration(ORMObject $from) 
    {
	    
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
	            case 'arrayOfObject':
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
	            case 'arrayOfObject':
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
	    return Classes::getInheritanceOfClass(static::$object_infos['name'], $full);
	}
	
	/**
	 * So called complex hooks use this method
	 * @param string $action
	 * @param string $hook
	 * @param string $subaction
	 * @param unknown $destination
	 */
	protected function setComplexHook(string $action, string $hook, string $subaction, $destination) 
    {
	    $this->hooks[$action][$subaction][] = array('destination'=>$destination,'hook'=>$hook);
	    
	    $parts = explode('.',$subaction);
	    $field = array_shift($parts);
	    $restaction = implode('.',$parts);
	    $property = $this->getProperty($field);
	    $property->addHook($action,$hook,$restaction,$destination);
	//    $this->addHook('EXTERNAL','complex_changed',$field,$this,array('action'=>$action,'hook'=>$hook,'field'=>$restaction));
	}
	
	protected function setExternalHook(string $action, string $subaction, $destination, $payload, string $hook)
    {
	    parent::setExternalHook($action,$subaction,$destination,$payload,$hook);
        $this->getProperty('externalhooks')->setDirty(true);
	}
	
	public function arrayFieldNewEntry($name,$index,$value) 
    {
	    $this->checkForHook('PROPERTY_ARRAY_NEW',$name,[$value]); 
	}
	
	public function arrayFieldRemovedEntry($name,$index,$value) 
    {
	    $this->checkForHook('PROPERTY_ARRAY_REMOVED',$name,[$value]);	    
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
	   $this->checkForHook('ATTRIBUTE_ADDING',$attribute->name,[$value]);
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
	            if (is_a($this,$class)) {
	                $allowed = true;
	            }
	        }
	    }
	    if (!$allowed) {
	        throw new \Sunhill\ORM\Properties\AttributeException(__("The attribute ':attribute' is not allowed for this object.",['attribute'=>$attribute->name]));
	    }	    
	}
	
	/**
	 * This routine is called, whenever a migration on the class was performed
	 * @param unknown $added_fields
	 * @param unknown $removed_fields
	 */
	public function objectMigrated(array $added_fields, array $removed_fields, array $changed_fields) 
    {
	    
	}
	
	// ********************** Static methods  ***************************	
	
	/**
	 * Initializes the properties of this object. Any child has to call its parents setupProperties() method
	 */
	protected static function setupProperties() 
    {
	    parent::setupProperties(); 
	    self::addProperty('tags','tags')->searchable();
	    self::timestamp('created_at');
	    self::timestamp('updated_at');
	}

	// ****************** Migration **********************************
	/**
	 * @deprecated The migration should be done via Classes facade. This method is to be removed
	 */
	public static function migrate() 
    {
        Classes::migrateClass(static::$object_infos['name']);
	}
	
	/**
	 * Traverses all classes in the hirachy and combines the static property $name in one resulting array
	 *
	 * @param string $name
	 * @return array
	 */
	public static function getHirarchicArray(string $name)
	{
	    if (! property_exists(get_called_class(), $name)) {
	        throw new ObjectException(__("The property ':name' doesn't exists.",['name'=>$name]));
	    }
	    $result = [];
	    $pointer = get_called_class();
	    do {
	        $result = array_merge($result, $pointer::$$name);
	        $pointer = get_parent_class($pointer);
	    } while (property_exists($pointer, $name)); // at least ORMObject shouldn't define it
	    return $result;
	}
	
	public static function searchKeyField(string $keyfield) 
    {
	   $query = static::search();
	   $keyfields = static::defineKeyFields($keyfield);
	   if (empty($keyfields)) {
	       throw new ObjectException(__("The class doesn't support keyfield search"));
	   }
	   foreach ($keyfields as $key => $value) {
	       $query = $query->where($key,$value);
	   }
	   return $query->loadIfExists();
	}
	
	protected static function defineKeyFields(string $keyfield) 
    {
	    
	}
	
}
