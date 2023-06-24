<?php
/**
 * @file PropertyCollection.php
 * Defines the class PropertyCollection. This is, as the name suggents, a class that has properties. 
 * It is an abstract class. The derrived classes ORMObject or Collection should be used
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
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Storage;

use Sunhill\ORM\Properties\Commitable;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyPropertyCollection;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Interfaces\InteractsWithStorage;
use Sunhill\ORM\Properties\PropertyTags;
use Sunhill\ORM\Objects\StorageInteraction\CollectionLoader;
use Sunhill\ORM\Objects\StorageInteraction\StorageInteractionBase;

/**
 * Basic class for all classes that have properties.
 *  
 * @author lokal
 */
abstract class PropertiesCollection extends NonAtomarProperty implements \Sunhill\ORM\Properties\Utils\Commitable, InteractsWithStorage
{
            
    public function __construct()
    {
        parent::__construct();
        $this->initializeProperties();    
    }
    
    protected $id;

    public function getID()
    {
        return $this->id;     
    }
    
    protected function setID($id)
    {
        $this->id = $id;
    }
    
    abstract public function getIDName(): string;    
    abstract public function getIDType(): string;
    
    protected function initializeProperties()
    {
        $this->properties = static::getAllPropertyDefinitions(); 
        $this->walkProperties(function($property, $owner) { 
            $property->setActualPropertiesCollection($owner); 
        },$this);
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
        
        $static = static::getPropertyDefinition($this);
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
        } while ($class != PropertiesCollection::class);
        
    }
// ================================== Attributes =================================================
    protected $dynamic_properties = [];
    
    protected function createProperty(string $type)
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return new PropertyInteger();
                break;
            case 'varchar':
            case 'string':
            case 'char':    
                return new PropertyVarchar();
                break;
            case 'float':
                return new PropertyFloat();
                break;
            case 'text':
                return new PropertyText();
                break;
            case 'date':
                return new PropertyDate();
                break;
            case 'datetime':
                return new PropertyDateTime();
                break;
            case 'time':
                return new PropertyTime();
                break;
            case 'array':
                return new PropertyArray();
                break;
            case 'map':
                return new PropertyMap();
                break;
            case 'object':
                return new PropertyObject();
                break;
            case 'collection':
                return new PropertyPropertyCollection();
                break;
            default:
                throw new PropertyException("The type '$type' is not allowed for attributes.");
        }
    }
    
    protected function dynamicAddProperty(string $name,string $type)
    {
        $property = $this->createProperty($type);        
        $property->setOwner(static::class);
        $property->setName($name);
        
        $this->properties[$name] = $property;
        $this->dynamic_properties[$name] = $property;
        
        return $property;
    }
    
    public function getDynamicProperties()
    {
        return $this->dynamic_properties;
    }
    
    // ================================ Static Properties ============================================    
    public static function hasNoOwnProperties()
    {
        $reflector = new \ReflectionMethod(static::class, 'setupProperties');
        return ($reflector->getDeclaringClass()->getName() !== static::class);
    }
    
    /**
     * A static method that returns all properties that this class defines (not the inherited)
     * 
     * @return array
     * 
     * test: tests/Unit/Objects/PropertyCollection_PropertyTest::testDummyCollectionStatic()
     */
    public static function getPropertyDefinition(): array
    {
        $list = new PropertyList(static::class);
        
        if (!static::hasNoOwnProperties()) {            
            static::setupProperties($list);
        }
        
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
    public static function getAllPropertyDefinitions($owner = null): array
    {
        $result = [];
       
        static::traverseInheritance(function($class) use (&$result, $owner) {
            $result = array_merge($result, $class::getPropertyDefinition($owner));
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
	
	abstract public static function search();
	
	public function commit()
	{
	    $this->doCommit();
	    $this->walkProperties(function($property) { $property->commit(); } );	    
	}
	
	public function rollback()
	{
        $this->doRollback();
	    $this->walkProperties(function($property) { $property->commit(); } );
	}
	
	protected function doCommit()
	{
	    
	}
	
	protected function doRollback()
	{
	    
	}
	
	protected static function getStorage()
	{
	    $storage = Storage::createStorage();
	    $storage->setSourceType(static::$storageClass);
	    return $storage;
	}
	
	public static function delete($id)
	{
	   $storage = static::getStorage();
	   static::prepareStorage($storage);
	   $storage->delete($id);
	}
	
	public static function drop()
	{
	    $storage = static::getStorage();
	    static::prepareStorage($storage);
	    $storage->drop();	    
	}
	
	abstract protected function getLoaderInteraction(): StorageInteractionBase;
	
	public function loadFromStorage(StorageBase $storage)
	{	    
	    $interactor = $this->getLoaderInteraction();
	    $interactor->setStorage($storage)->setPropertiesCollection($this);
	    $interactor->run();
	}
	
	abstract protected function getStorerInteraction(): StorageInteractionBase;
	
	public function storeToStorage(StorageBase $storage)
	{
	    $interactor = $this->getStorerInteraction();
	    $interactor->setStorage($storage)->setPropertiesCollection($this);
	    $interactor->run();	    
	}
	
	abstract protected function getUpdaterInteraction(): StorageInteractionBase;
	
	public function updateToStorage(StorageBase $storage)
	{
	    $interactor = $this->getUpdaterInteraction();
	    $interactor->setStorage($storage)->setPropertiesCollection($this);
	    $interactor->run();	    
	}
	
}
