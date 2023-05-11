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

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Search\QueryBuilder;
use Sunhill\ORM\Facades\Storage;

/**
 * Basic class for all classes that have properties.
 *  
 * @author lokal
 */
class PropertyCollection extends NonAtomarProperty 
{
    
        
    public function __construct()
    {
        parent::__construct();
        $this->initializeProperties();    
    }
    
    protected $id;
    
    protected function setID(int $id)
    {
        $this->id = $id;    
    }
    
    public function getID(): int
    {
        return $this->id;
    }
    
    protected function initializeProperties()
    {
        $this->properties = static::getAllPropertyDefinitions();        
    }
    
    protected $properties = [];
    
    /**
     * Helper method that walks the properties and calls a method for every property
     *
     * @param string $callback
     * @param unknown $payload
     * @throws PropertyException
     */
    private function walkPropertiesMethod(string $callback, $payload)
    {
        if (!method_exists($this,$callback)) {
            throw new PropertyException("The method '$callback' doesnt exist.");
        }
        foreach ($this->properties as $name => $property) {
            $this->$callback($property, $payload);
        }
    }
    
    /**
     * Helper method that walks the properties and calls a closure for every property
     *
     * @param callable $callback
     * @param unknown $payload
     */
    private function walkPropetiesClosure(callable $callback, $payload)
    {
        foreach ($this->properties as $name => $property) {
            $callback($property, $payload);
        }
    }
    
    /**
     * Calls for every property the given callback
     *
     * @param string|callable $callback when this is a string it is consired as a method, otherwise
     * is has to be a closure
     * @throws PropertyException
     */
    protected function walkProperties($callback, $payload = null)
    {
        if (is_string($callback)) {
            $this->walkProperiesMethod($callback, $payload);
        } else if (is_callable($callback)) {
            $this->walkPropetiesClosure($callback, $payload);
        } else {
            throw new PropertyException("walkProperties: callback is neither a method name nor a closure");
        }
    }
    
    /**
     * Indicates the storage class
     * @var string
     */
    protected $storageClass = 'Collection';
    
// ================================== Infos ===============================================
    /**
     * Stores the collection infos
     * @var unknown
     */
    protected static $infos;
    
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
     * 
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
// ============================= Dynamic Properties =======================================
    /**
     * Returns true if this property is dirty
     * Extends Property::isDirty() by checking if any of the sub properties are dirty
     * 
     * @return bool true if this collection or a property is dirty otherwise false
     * 
     * Test: /Unit/Objects/PropertyCollection_PropertyTest::testDummySetValue()
     */
    public function isDirty(): bool
    {
        if ($this->dirty) {
            return true;
        }
        foreach ($this->getAllProperties() as $key => $property) {
            if ($property->isDirty()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Return true if the given property exsists
     * 
     * @param string $name
     * @return bool
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionNonStatic()
     */
    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }
    
    /**
     * Returns the property object of the given property or raises an expcetion if this 
     * doesn't exist. 
     * 
     * @param string $name
     * @throws PropertyException
     * @return Property
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionNonStatic()
     */
    public function getProperty(string $name): Property
    {
        if (!$this->hasProperty($name)) {
            throw new PropertyException("The property '$name' does not exist.");
        }
        
        return $this->properties[$name];
    }
    
    /**
     * Returns a list of properties that are defined by this class
     * 
     * @return array
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionNonStatic()
     */
    public function getProperties(): array
    {
        $result = [];
        
        $static = static::getPropertyDefinition();
        foreach ($static as $key => $property) {
            $result[$key] = $this->properties[$key];
        }
        
        return $result;
    }
    
    /**
     * Returns a list of properties that are defined by this class and all parents
     * 
     * @return array
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionNonStatic()
     */
    public function getAllProperties(): array
    {
        return $this->properties;        
    }
    
    // ================================ Inheritance ===========================================
    /**
     * Walks through the inheritance and calls $callback for every class
     * 
     * @param callable $callback
     */
    protected static function traverseInheritance(callable $callback)
    {
        $class = static::class;
        do {
            $callback($class);
            $class = get_parent_class($class);
        } while ($class != PropertyCollection::class);
        
    }
    
// ================================ Static Properties ============================================    
    /**
     * A static method that returns all properties that this class defines (not the inherited)
     * 
     * @return array
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionStatic()
     */
    public static function getPropertyDefinition(): array
    {
        $list = new PropertyList(null);
        
        static::setupProperties($list);
        
        return $list->toArray();
    }

    /**
     * A static method that returns if this class defines the given property (but not if inherited)
     * 
     * @param string $name
     * @return bool
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionStatic()
     */
    public static function definesProperty(string $name): bool
    {
        return in_array($name, array_keys( static::getPropertyDefinition() ));
    }

    /**
     * A static method that returns all properties 
     * 
     * @return array
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionStatic()
     */
    public static function getAllPropertyDefinitions(): array
    {
        $result = [];
       
        static::traverseInheritance(function($class) use (&$result) {
            $result = array_merge($result, $class::getPropertyDefinition());
        });
        
        return $result;
    }
    
    public static function getPropertyObject(string $name): ?Property
    {
        $properties = static::getAllPropertyDefinitions();
        if (isset($properties[$name])) {
            return $properties[$name];
        }
        return null;
    }
    
	protected static function setupProperties(PropertyList $list)
	{	
	}
	
	public static function search() {
	    $query = new QueryBuilder();
	    $query->setCallingClass(get_called_class());
	    return $query;
	}
	
	/**
	 * Loads the collection with the id $id from the storage
	 * In this case it sets the state to preloaded. Accessing the properties wouhl than execute
	 * the loading mechanism.
	 * 
	 * @param int $id
	 */
	public function load(int $id)
	{
	    $this->setState('preloaded');
	    $this->setID($id);
	}

	/**
	 * Umplements the lazy loading mechanism, that a collection is only loaded if accessed
	 * 
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\NonAtomarProperty::checkLoadingState()
	 */
	protected function checkLoadingState()
	{
	    if ($this->getState() == 'preloaded') {
	        $this->finallyLoad();
	    }
	}
	
	/**
	 * Does finally load the collection from the database
	 */
	protected function finallyLoad()
	{
	    $storage = Storage::createStorage($this);
	    $storage->load($this->getID());
	    $this->setState('loading');
	    $this->loadFromStorage($storage);	    
	}
}
