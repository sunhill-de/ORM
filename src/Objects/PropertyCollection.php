<?php
/**
 * @file PropertyCollection.php
 * Defines the class PropertyCollection. This is, as the name suggents, a class that has properties. 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: unknown
 * PSR-State: some type hints missing
 * Tests: PropertyCollection_infoTest
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Search\QueryBuilder;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\PropertyQuery\PropertyQuery;
use Sunhill\ORM\Properties\Property;

/**
 * Basic class for all classes that have properties.
 *  
 * @author lokal
 */
class PropertyCollection extends Property 
{
	
    protected $properties;
    
    protected static $infos;

    protected $storageClass = 'Collection';
    
	/**
     * Constructor calls setupProperties()
     */
	public function __construct() 
        {
		parent::__construct();
		self::initializeProperties();
		$this->copyProperties();
	}
		
// ==================================== Loading =========================================
	
 	/**
	 * Checks if the object with the given id is already in cache
	 * @param integer $id The id to check
     * @returns bool True if it is in the cache, false if not
	 */
	protected function check_cache(int $id): bool 
    {
	    return false;
	}
	
	/**
	 * Add itself to the cache
	 * @param Int $id
	 */
	protected function insert_cache(int $id) 
    {
	}
	
    /**
     * Does the loading (has to be overwritten)
     */
	protected function doLoad(StorageBase $loader)
	{
        if ($result = $this->checkCache($id)) { // Is this object already in the cache
            $this->setState('invalid'); // If yes, this object is invalid
            return $result; // Return the cache instead
        }
        
        $this->current_storage = $loader;
        $this->insertCache($id); // Insert into cache
        $this->setID($id);
        
        $this->cleanProperties();
        
        return $this;
	}
	
	
// ====================================== Updating ========================================	
    /**
     * Does the update work
     */
	protected function doUpdate(StorageBase $storage, string $name) 
    {
	    // has to be overwritten in child objects
	}
	
	/**
	 * Cleans the dirty state
	 */
	protected function clearDirty() 
    {
	    $this->cleanProperties();
	}

// ======================================= Inserting ===========================================
	/**
	 * Does the insert work
	 * @param bool $recommit
	 */
	protected function doInsert(StorageBase $storage, string $name) 
    {
	   // has to be overwritten in child objects 
	}
	
	// ====================================== Deleting ==========================================
    /**
     * Does the delete work
     */
	protected function doDelete() 
    {
        $this->clearCacheEntry();
        // Has to be overwritten in child objects 
	}
	
    /**
     * Clears the cache (reomves this entry)
     */
	protected function clearCacheEntry() 
    {
	   // Has to be overwritten in child objects 
	}
	
	// ===================================== Property-Handling ========================================	

    /**
	 * Is called by the constructor to initialize the properties
     * Child objects always have to call the parent method
     */
	protected function copyProperties() 
    {
	    $this->properties = array();
	    foreach (static::$property_definitions as $name => $property) {
	        $this->properties[$name] = clone $property;
	        //$this->properties[$name]->setClass(getClass($this));
	        $this->properties[$name]->setOwner($this);
	    }
	}

    /**
     * Undirties all properties 
     */
	public function cleanProperties() 
    {
	    foreach ($this->properties as $property) {
	        $property->setDirty(false);
	    }
	}
	
    /**
     * Searches for a property with the given name. If there is one, return its value. If not pass it to the parent __get method
     * @param $name string The name of the unknown member variable
     */
	public function &__get($name) 
    {
	    if (isset($this->properties[$name])) {
            $this->checkLoadingState(); // Is this object only preloaded?
	        return $this->properties[$name]->getValue();
	    } else {
	        $help = parent::__get($name);
	        return $help;
	    }
	}

    /**
     * Searches for a property with the given name. If there is one, set its value. if not call handleUnknownProperty()
     * @param $name string The name of the unknown member variable
     * @param $value void The valie for this member variable
     */
	public function __set(string $name, $value) 
    {
	    if (isset($this->properties[$name])) {
	        if ($this->getReadonly()) {
	            throw new PropertyCollectionException(__("Property ':name' was changed in readonly state.",['name'=>$name]));
	        } else {
	            $this->checkLoadingState(); // Is this object only preloaded?
	            $this->properties[$name]->setValue($value);
	        }
	    } else if (!$this->handleUnknownProperty($name,$value)){
	        throw new PropertyCollectionException(__("Unknown property ':name'",['name'=>$name]));
	    }
	}
	
	/**
	 * Tries to handle an unknown property. If it can't be handled return false, then an exception will be raised
	 * @param unknown $name The Name of the property
	 * @param unknown $value The value of the property
	 * @return boolean
	 */
	protected function handleUnknownProperty(string $name, $value) 
    {
	   return false;    
	}
	
	/**
	 * Returns the property object with the given name or raises an exception if there is no such property
	 * @param string $name Name of the property
	 * @return Property
	 */
	public function getProperty(string $name, bool $return_null = false) 
    {
	    if (!isset($this->properties[$name])) {
	        if ($return_null) {
	            return null;
	        }
	        throw new PropertyCollectionException(__("Unknown property ':name'",['name'=>$name]));
	    }
	    return $this->properties[$name];
	}
	
	/**
	 * @deprecated Use getProperties() query instead
	 * Return all properties that have a certain feature
	 * @param string $feature, if not null then only properties with this feature are returned
         * @param bool $dirty, if true only dirty properties are returned
         * @param string $group, if not null the result is grouped by this feature
	 * @return unknown[]
	 */
	public function getPropertiesWithFeature(string $feature = '', $dirty = null, $group = null) 
    {
	    $result = array();
	    if (isset($group)) {
	        $group = 'get_'.$group;
	    }
	    foreach ($this->properties as $name => $property) {
	        // Should dirty be considered?
	        if (isset($dirty)) {
	            if ($dirty && (!$property->getDirty())) {
	                continue;
	            } else if (!$dirty && ($property->getDirty())) {
	                continue;
	            }
	        }
	        if (empty($feature)) { // Are there features to consider ?
	            if (isset($group)) { // Should we group ?
	                $group_value = $property->$group();
	                if (isset($result[$group_value])) {
	                    $result[$group_value][$name] = $property;
	                } else {
	                    $result[$group_value] = array($name=>$property);
	                }
	            } else {
	                $result[$name] = $property;
	            }
	        } else {
	           if ($property->hasFeature($feature)) {
	               if (isset($group)) { // Should be grouped ?
	                   $group_value = $property->$group();
	                   if (isset($result[$group_value])) {
	                       $result[$group_value][$name] = $property;
	                   } else {
	                       $result[$group_value] = array($name=>$property);
	                   }
	               } else {
	                   $result[$name] = $property;
	               }
	           }
	        }
	    }
	    return $result;
	}

	/**
	 * Return a ProperyQuery class for further definition of the desired properties
	 * @return \Sunhill\ORM\Objects\PropertyQuery
	 */
	public function getProperties()
	{
	   return new PropertyQuery($this->properties);    
	}
	
	protected function dynamicAddProperty(string $name,string $type) 
    {
	    $property = static::createProperty($name, $type);
	    $property->setOwner($this);
	    $this->properties[$name] = $property;
	    return $property;	    
	}
	
	public function getDynamicProperties()
	{
	   $result = [];
	   $all_properties    = $this->getProperties()->get();
	   $static_properties = static::staticGetProperties()->get();
       $static_properties['externalhooks'] = 'externalhooks';
	   foreach ($all_properties as $key => $property) {
	       if (!array_key_exists($key,$static_properties)) {
	           $result[$key] = $property;
	       }
	   }
	   return $result;
	}
	
	// ========================== Static methods ================================
	
	protected static $property_definitions;
	
	/**
	 * Return a ProperyQuery class for further definition of the desired properties
	 * Static variant of getProperties
	 * @return \Sunhill\ORM\Objects\PropertyQuery
	 * Test: trivial, just returns a PropertyQuery
	 */
	public static function staticGetProperties(): PropertyQuery
	{
	   	static::initializeProperties();
	   	return new PropertyQuery(static::$property_definitions);    
	}
	
	/**
	 * Creates an empty array for properties and calls setupProperties()
	 * test: Trivial, nothing to test
	 */
	public static function initializeProperties() 
        {
 	       static::$property_definitions = array();
	       static::setupProperties();
	}

	/**
	 * This is the core static method that derrived classes have to call to define their
	 * properties.
	 * Test: nothing to test        
	 */
	protected static function setupProperties() 
    {
	    
	}

	/**
	 * Creates an empty array for infos and calls setupInfos()
	 * Infos are class wide additional informations that are stored to a class. Useful for information
	 * like name, table-name, editable, etc.	 
	 * Test: /Unit/Objects/PropertyCollection_infoTest
	 */
	protected static function initializeInfos()
	{
		static::$infos = [];
		static::setupInfos();
	}
	
	/**
	 * This method must be overwritten by the derrived class to define its infos
	 * Test: /Unit/Objects/PropertyCollection_infoTest
	 */
	protected static function setupInfos()
	{
	    static::addInfo('name', 'PropertyCollection');
	    static::addInfo('table', '');
	    static::addInfo('name_s', 'properties having');
	    static::addInfo('name_p', 'properties having');
	    static::addInfo('description', 'A base class that defines properties.');
	    static::addInfo('options', 0);
	}

	/**
	 * Adds an entry to the class definition
	 * @param string $key: The key for the piece of information
	 * @param unknown $value: The value of this information
	 * @param bool $translatable: A boolean that indicates, if the return should pass the __() function
	 * Test: /Unit/Objects/PropertyCollection_infoTest
	 */
	protected static function addInfo(string $key, $value, bool $translatable = false)
	{
		$info = new \StdClass();
		$info->key = $key;
		$info->value = $value;
		$info->translatable = $translatable;
		static::$infos[$key] = $info;
	}
	
	/**
	 * returns the Information named $key
	 * @param string $key
	 * @throws PropertyCollectionException
	 * @return string|array|NULL|unknown
	 * Test: /Unit/Objects/PropertyCollection_infoTest
	 */
	public static function getInfo(string $key, $default = null)
	{
        static::initializeInfos();
	    if (!isset(static::$infos[$key])) {
	        if (is_null($default)) {
			    throw new PropertyCollectionException("The key '$key' is not defined.");
	        } else {
	            return $default;
	        }
		}	
		$info = static::$infos[$key];
		if ($info->translatable) {
			return static::translate($info->value);
		} else {
			return $info->value;
		}	
	}
	
	/**
	 * Return all avaiable infos
	 * @return unknown
	 * Test: /unit/Ovhects/PropertyCollection_infoTest
	 */
	public static function getAllInfos()
	{
	    static::initializeInfos();
	    return static::$infos;    
	}
	
	/**
	 * Checks if the given info is defined
	 * @param string $key
	 * @return bool
	 * Test: /unit/Ovhects/PropertyCollection_infoTest
	 */
	public static function hasInfo(string $key): bool
	{
	    static::initializeInfos();
	    return isset(static::$infos[$key]);
	}
	
	/**
	 * Wrapper for the __() function
	 * @param unknown $info
	 * @return string|array|NULL
	 * Test: /Unit/Objects/PropertyCollection_infoTest
	 */
	protected static function translate(string $info): string
	{
		return __($info);
	}
	
	/**
	 * This method returns the class that called one of the Property methods
	 * It is used only internally and uses a quite dirty hack (debug_backtrace()) so
	 * this method is not well testable (But at the moment it works)
	 * @return string
	 * Test: /unit/Ovhects/PropertyCollection_PropertyTests::testGetCallingClass
	 */
	protected static function getCallingClass(): string 
    {
	    $caller = debug_backtrace();
	    return $caller[5]['class'];
	}
	
	/**
	 * Returns the class name of the given property or raises an excpetion if none exists
	 * @param unknown $type
	 * @throws ORMException
	 * @return string
	 * Test: 
	 * - /unit/Ovhects/PropertyCollection_PropertyTests::testGetPropertyClass, 
	 * - /unit/Ovhects/PropertyCollection_PropertyTests::testGetPropertyclass_failure
	 */
	protected static function getPropertyClass($type): string
	{
	    $type = ucfirst($type);
	    $property_name = '\Sunhill\ORM\Properties\Property'.$type;
	    if (!class_exists($property_name)) {
	        throw new ORMException("The property type '$property_name' does not exists");
	    }
	    return $property_name;
	}

	/**
	 * This routine was necessary because of a kind of circular dependency. 
	 * @param unknown $class
	 * @return unknown|string
	 * Test: no test at the momement, would be great to get rid of it!
	 */
	protected static function getCallingClassname($class)
	{
	    if (!is_null($class)) {
	        return $class;
	    }
	    $class = Classes::searchClass(static::getCallingClass());
	    if (is_null($class)) {
	        return '';
	    } else {
	        return $class;
	    }
	}
	
	/**
	 * Creates a new property object
	 * @param string $name
	 * @param string $type
	 * @param unknown $class
	 * @return unknown
	 * Test: /Unit/Objects/PropertyCollection_PropertyTests::testCreateProperty
	 * Note: setClass is not unit testable because of the use of getCallingClass (see above)
	 */
	protected static function createProperty(string $name, string $type, $class = null) 
    {
	    $property_name = static::getPropertyClass($type);
        $property = new $property_name();
	    $property->setName($name);
	    $property->setType($type);
	    $property->setClass(static::getCallingClassname($class));
	    $property->initialize();
	    return $property;
	}
	
	/**
	 * Adds a property with the given name and the given type
	 * @param string $name
	 * @param string $type
	 * @return \Sunhill\ORM\Objects\unknown
	 * test: Untestable due the use of getCallingClass (see above)
	 */
	protected static function addProperty(string $name, string $type): Property 
    {
	    $property = static::createProperty($name, $type);
	    static::$property_definitions[$name] = $property;
	    return $property;
	}
	
	/**
	 * Returns the Propertyobject of the given property or null
	 * @param string $name
	 * @return unknown|NULL
	 * Test: 
	 * - /Unit/Objects/PropertyCollection_PropertyTests::testGetPropertyObject
	 */
	public static function getPropertyObject(string $name): ?Property 
    {
	    static::initializeProperties();
	    if (isset(static::$property_definitions[$name])) {
	        return static::$property_definitions[$name];
	    } else {
	        return null;
	    }
	}

	/**
	 * Alias to getPropertyObject
	 * @param string $name
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: /Unit/Objects/PropertyCollection_PropertyTests::testPrepareGroup 
	 */
	public static function getPropertyInfo(string $name): ?Property
	{
	    return static::getPropertyObject($name);
	}
	
	/**
	 * Prepares the group parameter for a grouping method if not null
	 * 
	 * @param unknown $group
	 * @return string|NULL
	 * Test: /Unit/Objects/PropertyCollection_PropertyTests::testPrepareGroup 
	 */
	protected static function prepareGroup($group): ?string
	{
	   return isset($group)?'get'.ucfirst($group):null;    
	}
	
	/**
	 * return all defined (static) properties
	 *
	 * @param unknown $group
	 * @return string|NULL
	 * Test: /Unit/Objects/PropertyCollection_PropertyTests::testGetAllProperties
	 */
	protected static function getAllProperties(): array
	{
	   static::setupProperties();
	   return static::$property_definitions;
	}

	/**
	 * Filters the given input array if a feature is set
	 * 
	 * @param array $input
	 * @param string $feature
	 * @return array
	 * Test: /Unit/Objects/PropertyCollection_PropertyTests::testFilterFeature
	 */
	protected static function filterFeature(array $input, string $feature): array
	{
	    if (empty($feature)) {
	        return $input;
	    }
	    $result = [];
	    foreach ($input as $name => $property) {
	        if ($property->hasFeature($feature)) {
	            $result[$name] = $property;
	        }
	    }
	    return $result;
	}
	
	/**
	 * Group the results of the properties by the given group
	 * 
	 * @param array $input
	 * @param unknown $group
	 * @return array
	 * Test: /Unit/Objects/PropertyCollection_PropertyTest::testGroupResult
	 */
	protected static function groupResult(array $input, $group): array
	{
	    if (empty($group)) {
	        return $input;
	    }
	    $group = static::prepareGroup($group);
	    $result = [];
	    foreach ($input as $name => $property) {
	        $group_value = $property->$group();
	        if (isset($result[$group_value])) {
	            $result[$group_value][$name] = $property;
	        } else {
	            $result[$group_value] = [$name => $property];
	        }
	    }
	    return $result;
	}
	
	/**
     * Returns all properties that have a certain feature and group them 
	 * @param string $feature, if not null returns only properties that have this feature 
	 * @param string $group, if not null the results are grouped defined by $group
	 * @return unknown[]
	 */
	public static function staticGetPropertiesWithFeature(string $feature = '', $group = null): array 
    {
	    $result = array();
	    return static::groupResult(
	                   static::filterFeature(
	                           static::getAllProperties(), $feature), $group);
	}
	
	public static function search() {
	     $query = new QueryBuilder();
	     $query->setCallingClass(get_called_class());
	     return $query;
	}
	
	/**
	 * Creates a timestamp property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function timestamp($name) {
	    $property = self::addProperty($name, 'timestamp');
	    return $property;
	}
	
	/**
	 * Creates an integer property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function integer($name) 
    {
	    $property = self::addProperty($name, 'integer');
	    return $property;
	}
	
	/**
	 * Creates a character property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function varchar($name) 
    {
	    $property = self::addProperty($name, 'varchar');
	    return $property;
	}
	
	/**
	 * Creates an object property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function object($name) {
	    $property = self::addProperty($name, 'object');
	    return $property;
	}
	
	/**
	 * Creates a text property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function text($name) 
    {
	    $property = self::addProperty($name, 'text');
	    return $property;
	}
	
	/**
	 * Creates an enum property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function enum($name) 
    {
	    $property = self::addProperty($name, 'enum');
	    return $property;
	}
	
	/**
	 * Creates a date property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function datetime($name) 
    {
	    $property = self::addProperty($name, 'datetime');
	    return $property;
	}
	
	/**
	 * Creates a datetime property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function date($name) 
    {
	    $property = self::addProperty($name, 'date');
	    return $property;
	}
	
	/**
	 * Creates a time property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function time($name) 
    {
	    $property = self::addProperty($name, 'time');
	    return $property;
	}
	
	/**
	 * Creates a float property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function float(string $name): Property
    {
	    $property = self::addProperty($name, 'float');
	    return $property;
	}
	
	/**
	 * Creates an array of strings property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function arrayOfStrings(string $name): Property
    {
	    $property = self::addProperty($name, 'arrayOfStrings');
	    return $property;
	}
	
	/**
	 * Creates an array of objects property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function arrayOfObjects(string $name): Property
    {
	    $property = self::addProperty($name, 'arrayOfObjects');
	    return $property;
	}
	
	/**
	 * Creates an calculated property
	 * 
	 * @param string $name The name of this property
	 * @return \Sunhill\ORM\Properties\Property
	 * Test: testPropertyMethods
	 */
	protected static function calculated(string $name): Property 
    {
	    $property = self::addProperty($name, 'calculated');
	    return $property;
	}
	
}
