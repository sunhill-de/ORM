<?php

/**
 * @file Property.php
 * Provides an access to a single property of an object 
 * Lang de,en
 * Reviewstatus: 2020-08-06
 * Localization: incomplete
 * Documentation: incomplete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

/**
 * @todo It has to be considered, if an direct DB access is necessary in properties or if they should 
 * be layed out completely to the storage
 */
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\Basic\Loggable;
use Sunhill\ORM\PropertyCollection;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Semantic\Name;
use TijsVerkoyen\CssToInlineStyles\Css\Property\Processor;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use Sunhill\ORM\Properties\PropertyException;

/** 
 * These constants are used in get_diff_array as an optional parameter. They decide how object references should 
 * be treated. 
 * @var unknown
 */

/**
 * The values are copied. For object references that means that these are copied. 
 */
define ('PD_VALUE',1);
/**
 * If we have object references the IDs are copied
 */
define ('PD_ID',2);
/**
 * If an object reference is already loaded, the object is returned otherwise the ID
 */
define ('PD_KEEP',3);  

/**
 * A basic class for properties. 
 * @author lokal
 *
 */
class Property extends Loggable 
{
	
    /**
     * Properties get the possibility to add additinal fields (like property->set_additional)
     */
    private $additional_fields = [];
        
    protected $state = 'normal';
    
    /**
     * This field stores the owner of this property. It points to an descendand of PropertyCollection 
     * Property->getOwner() reads, Property->setOwner() writes
     * @var \Sunhill\ORM\PropertyCollection
     */
	protected $owner;
	
    /**
     * The name of this property
     * Property->getName() reads, Property->setName() writes
     * @var string
     */
	protected $name;
	
	/**
	 * The value of this property
	 * @var void
	 */
	protected $value;
	
	/**
	 * Does this property has a unit (by default none)
	 * @var unknown
	 */
	protected $unit = None::class;
	
	/**
	 * The semantic meaning of this property (by default name)
	 * @var unknown
	 */
	protected $semantic = Name::class;
	
	/**
	 * Is this property allowed to take null as a value (by default yes)
	 * @var boolean
	 */
	protected $nullable = true;
	
	/**
	 * The shadow value of this property. This is the value after the last Property->commit()
     * It is used for rollback and creation of the diff array (Property->getDiffArray())
	 * @var void
	 */
	protected $shadow;
	
	/**
	 * The type of this property. Is set by the property itself and can't (or shouldn't) be changed
	 * @var string
	 */
	protected $type = '';
	
	/**
	 * The default value for the value field. In combination with Property->defaults_null this default value 
     * is used:
     * $default  | $defaults_null | Default value
     * ----------+----------------+------------------------------
     * not null  | any            | the value stored in $default
     * null      | true           | null
     * null      | false          | no default value
     * With a default value an property is never unititialized
     * @var void
	 */
	protected $default;
	
	/**
	 * See above
	 * @var bool
	 */
	protected $defaults_null;
	
	/**
	 * Shows if this property is dirty. If false the value wasn't change since initialization or the last
     * commit. If true than it was changed. An access should be performed via Property->getDirty() and
     * Property->setDirty().
	 * @var bool
	 */
	protected $dirty=false;
		
	/**
     * Shows if the value was initialized at some time. If true that it was initialized already (even through
     * a default value or via loading). If false it was not initialied. A read access on a not initialized value
     * raises an excpetion.
	 * @var bool
	 */
	protected $initialized=false;
	
	/**
     * Shows if the property is read only (true) or writable (false)
	 * @var bool
	 */
	protected $read_only=false;
	
	/**
	 * The name of the associated validator. By default it's a validator that accepts any value
	 * @var string
	 */
	protected $validator_name = 'ValidatorBase';
	
	/**
     * Stores the validator object
     * @var \Sunhill\ORM\Validators\ValidatorBase
	 */
	protected $validator;
	
	
    /**
     * Stores the class of the property
     * @var string
     */
	protected $class = '';
	
	/**
	 * Shows if this property is searchable (true) or not (false)
	 * @var bool
	 */
	protected $searchable=false;
	

	/**
	 * The constructor sets all values to a default
	 */
	public function __construct() 
	{
		$this->dirty = false;
		$this->defaults_null = false;
		$this->read_only = false;
		$this->initialize();
		$this->initValidator();
	}
	
	/**
	 * Extends the property with the possibility to deal with additional getters and setters
	 * 
	 * @param string $method
	 * @param array $params
	 * @return mixed|NULL|\Sunhill\ORM\Properties\Property
	 * 
	 * Test: /Unit/Properties/PropertyTest::testAdditionalGetter
	 * Test: /Unit/Properties/PropertyTest::testUnknownMethod
	 */
	public function __call(string $method, array $params) 
	{
         if (substr($method,0,3) == 'get') {
	        $name = strtolower(substr($method,3));
	        if (isset($this->additional_fields[$name])) {
	            return $this->additional_fields[$name];
	        } else {
	            return null;
	        }
	    } else if (substr($method,0,3) == 'set') {
	        $name = strtolower(substr($method,3));
	        $this->additional_fields[$name] = $params[0];
	        return $this;
	    }
	    throw new PropertyException("Unknown method '$method' called");
	}
	
	/**
	 * A method to provide the possibility to initialize this property. Is not the same as
     * setting initialized to true.
	 */
	public function initialize() 
	{
	}
	
	/**
	 * Initializes the validator 
	 * @throws PropertyException if the validator class dosn't exist
	 */
	protected function initValidator() 
	{
	    $validator_name = "\\Sunhill\\ORM\\Validators\\".$this->validator_name;
	    if (!class_exists($validator_name)) {
	        throw new PropertyException(__("Unknown validator ':validator' called.",['validator'=>$this->validator_name]));
	    }
	    $this->validator = new $validator_name();    
	}

	// ============================== State-Handling ===========================================
	
	/**
	 * Sets the current state of this object
	 * @param $state string the new state
	 */
	protected function setState(string $state): Property
	{
	    $this->state = $state;
	    return $this;
	}
	
	/**
	 * Returns the current state of this object
	 * @return string
	 */
	protected function getState(): string
	{
	    return $this->state;
	}
	
	/**
	 * Returns true if this object is comitting right now
	 * @return bool
	 */
	protected function isCommitting(): bool
	{
	    return ($this->getState() == 'committing');
	}
	
	/**
	 * Returns true if this object is invalid
	 * @return bool
	 */
	protected function isInvalid(): bool
	{
	    return $this->getState() == 'invalid';
	}
	
	/**
	 * Returns true if this object is loading right now
	 * @return bool
	 */
	protected function isLoading(): bool
	{
	    return $this->getState() == 'loading';
	}
	
	/**
	 * Raises an exception if the property is invalid
	 */
	protected function checkValidity()
	{
	    if ($this->isInvalid()) {
	        throw new PropertyException(__('Invalidated property called.'));
	    }
	}
	
	// =========================== Setter and getter ========================================	
    /**
     * sets the field Property->owner
     * 
     * @param $owner a class of PropertyCollection
     * @return Property a reference to this to make setter chains possible
     * 
     * Test Unit/Properties/PropertyTest::testOwner
     */
    public function setOwner($owner): Property
    {
	    $this->owner = $owner;
	    return $this;	    
    }

    /**
     * Alias for setOwner()
     * 
     * @param Property $owner
     * @return \Sunhill\ORM\Properties\Property
     * 
     * Test Unit/Properties/PropertyTest::testOwner
     */
    public function owner(Property $owner): Property
    {
        return $this->setOwner($owner);    
    }
    
    /**
     * Returns the value of the owner field
     * @return PropertyCollection
     * 
     * Test Unit/Properties/PropertyTest::testOwner
     */
    public function getOwner(): Property
    {
	    return $this->owner;
    }
	
    /**
     * sets the field Property->name
     * @param $name The name of the property
     * @return Property a reference to this to make setter chains possible
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setName(string $name): Property
    {
	    $this->name = $name;
	    return $this;
    }
    
    /**
     * Alias for setName()
     * 
     * @param string $name
     * @return Property
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function name(string $name): Property
    {
       return $this->setName($name); 
    }
    
    /**
     * Returns the name of this property
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getName(): ?string 
    {
	    return $this->name;
    }
	
    /**
     * Setter for unit
     * @param string $unit
     * @return Property
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setUnit(string $unit): Property
    {
        $this->unit = $unit;
        return $this;
    }
    
    /**
     * alias for setUnit
     * @param string $unit
     * @return Processor
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function unit(string $unit): Property
    {
        return $this->setUnit($unit);
    }
    
    /**
     * getter for unit
     * @return string
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getUnit(): string
    {
        return $this->unit;    
    }
    
    /**
     * Setter for sematic
     * @param string $sematic
     * @return Property
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setSemantic(string $semantic): Property
    {
        $this->semantic = $semantic;
        return $this;
    }
    
    /**
     * alias for setSematic
     * @param string $sematic
     * @return Processor
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function semantic(string $semantic): Property
    {
        return $this->setSemantic($semantic);
    }
    
    /**
     * getter for sematic
     * @return string
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getSemantic(): string
    {
        return $this->semantic;
    }
    
    /**
     * sets the field Property->type
     * 
     * @param $type The type of the property
     * @return Property a reference to this to make setter chains possible
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setType(string $type)
    {
	    $this->type = $type;
	    return $this;
    }
	
    /**
     * Alias for setType
     * 
     * @param string $type
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function type(string $type)
    {
        return $this->setType($type);    
    }
    
    /**
     * Getter for $type
     * 
     * @return string
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getType(): string
    {
	    return $this->type;
    }
	
    /**
     * Setter for $class
     * 
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     * 
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setClass(string $class): Property 
    {
	    $this->class = $class;
	    return $this;
    }
	
    /**
     * Getter for $class
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getClass(): string 
    {
	    return $this->class;
    }
	
    /**
     * Setter for $readonly
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setReadonly(bool $value = true): Property 
    {
	    $this->read_only = $value;
	    return $this;
    }
	
    /**
     * Alias for setReadonly
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function readonly(bool $value = true): Property
    {
        return $this->setReadonly($value);    
    }
    
    /**
     * Getter for $readonly
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getReadonly(): bool
    {
	    return $this->read_only;
    }
	
    /**
     * Setter for $searchable
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setSearchable(bool $value = true): Property
    {
	    $this->searchable = true;
	    return $this;
    }

    /**
     * Alias for setSearchable()
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function searchable(bool $value = true): Property
    {
        return $this->setSearchable($value);
    }
    
    /**
     * Getter for $earchable
     *
     * @return bool
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getSearchable(): bool 
    {
	    return $this->searchable;
    }
	
    /**
     * sets the field Property->default (and perhaps Property->defaults_null too)
     *
     * @return Property a reference to this to make setter chains possible
     * 
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function setDefault($default): Property
    {
        if (!isset($default)) {
            $this->defaults_null = true;
        }
        $this->default = $default;
        return $this;
    }
    
    /**
     * Alias for setDefault()
     *
     * @return Property a reference to this to make setter chains possible
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function default($default)
    {
       return $this->setDefault($default);
    }
    
    /**
     * Returns the current default value
     *
     * @return null means no default value, DefaultNull::class means null is Default
     * otheriwse it return the default value
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getDefault()
    {
        if ($this->defaults_null) {
            return DefaultNull::class;
        }
        return $this->default;
    }
    
    /**
     * Is null the default value?
     * 
     * @return boolean
     * 
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getDefaultsNull(): bool
    {
        return $this->defaults_null;
    }
    
    /**
     * Marks this property as nullable (null may be assigned as value). If there is
     * not already a default value, set null as default too
     * 
     * @param bool $value
     * @return Property
     * 
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function nullable(bool $value = true): Property
    {
        $this->nullable = $value;
        if (!$this->defaults_null && !is_null($this->default)) {
            $this->default(null);
        }
        return $this;
    }

    /**
     * Alias for nullable()
     * 
     * @param bool $value
     * @return Property
     * 
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function setNullable(bool $value = true): Property
    {
        return $this->nullable($value);
    }
    
    /**
     * Alias for nullable(false)
     * 
     * @return Property
     * 
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function notNullable(): Property
    {
        return $this->nullable(false);    
    }
    
    /**
     * Getter for nullable
     * 
     * @return bool
     * 
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getNullable(): bool
    {
        return $this->nullable;
    }
    
// ============================== Value Handling =====================================	
	
    /**
     * Raises an expcetion when the property is readonly
     * @throws PropertyException
     */
    protected function checkReadonly()
    {
        if ($this->read_only) {
            throw new PropertyException("Write to a read only property.");
        }        
    }
    
    protected function checkPermission()
    {
        
    }
    
    /**
     * If the property is already dirty, don't overwrite shadow
     */
    protected function handleShadow()
    {
        if (!$this->dirty) {
            $this->shadow = $this->value;
            $this->dirty = true;
        }        
    }
    
    protected function handleValue($value)
    {
        if (is_null($value)) {
            if (!$this->nullable) {
                throw new PropertyException("Property is not nullable");
            }
        } else {
            $value = $this->validate($value);
        }
        $this->doSetValue($value);        
    }
    
    protected function handleIndexValue($value, $index)
    {
        $this->doSetIndexedValue($index,(is_null($value)?null:$this->validate($value)));        
    }
    
    /**
	 * Writes the value of this property
	 * @param unknown $value
	 * @param unknown $index
	 * @throws PropertyException
	 * @return \Sunhill\ORM\Properties\Property
	 */
	final public function setValue($value, $index = null)
	{
		$this->checkReadonly();
		$this->checkPermission();
		
		// Check if the value is really changed
		if ($this->initialized && ($value === $this->value)) {
    		return $this;
		}

		if (is_null($index)) {
	        $this->handleValue($value);
		} else {
            $this->handleIndexValue($value, $index);
		}
		    
		$this->handleShadow();
		$this->initialized = true;
		return $this;
	}

	/**
	 * Writes the new value 
	 * @param mixed $value
	 */
	protected function doSetValue($value) 
	{
	    $this->value = $value;
	}
	
	final public function &getValue() 
	{
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;
				$this->shadow = $this->default;
				$this->initialized = true;
			} else {
			    if (!$this->initializeValue()) {
			         throw new PropertyException(__("Read of a not initialized property: ':name'",['name'=>$this->name]));
			    }
			}
		}
		return $this->doGetValue();
	}

	/**
	 * A last possibility to initialize a value (e.g. calculated field)
	 * @return bool, true if successful otherwise false
	 */
	protected function initializeValue(): bool 
	{
	    return false;
	}
	
	protected function &doGetValue() 
	{
	    return $this->value;    
	}
	
	protected function &doGetIndexedValue($index) 
	{
	    return $this->value[$index];
	}
	
    /**
     * Returns the value of the shadow field 
     * @return void: The value of Property->shadow
     */
	public function getOldValue() 
	{
		return $this->shadow;
	}
	
	/**
	 * Creates a diff array
	 * This means the method creates an array with two named fields:
	 * FROM is the old value
	 * TO is the new value
     * If the property is dealing with object references the $type field is respected
	 * @param int $type One of the PD_XXXX fields (see above)
	 * @return void[]
	 */
	public function getDiffArray(int $type=PD_VALUE)
	{
	    return array('FROM'=>$this->getDiffEntry($this->shadow,$type),
	                 'TO'=>$this->getDiffEntry($this->value,$type));
	}
	
	/**
	 * Da der Typ bei den meisten Properties ignoriert wird, kann eine abgeleitete Property diese
	 * Methode überschreiben, um den Praemeter $type zu respektieren.
	 * @param unknown $entry
	 * @param int $type
	 * @return unknown
	 */
	protected function getDiffEntry($entry, int $type) 
	{
	    return $entry;
	}
//========================== Dirtyness ===============================================	
	
    /**
     * Tests, if the property is dirty
     * @return bool: True if it is dirty otherwise false 
     */
    public function getDirty()
    {
		return $this->dirty;	
	}
	
    /**
     * Sets the value of dirty to $value
     * @param bool $value The new value of dirty
     */
    public function setDirty(bool $value) 
    {
		$this->dirty = $value;
	}
	
	/**
	 * Returns, if the property is initialized
	 * 
	 * @return bool
	 */
	public function getInitialized(): bool
	{
	   return $this->initialized;    
	}
	
	/**
	 * A storage class indicates the storage facade, what kind of storage it has to
	 * create
	 * An empty value means, that this property cannot store itself into the storage
	 * 
	 * @return string
	 */
    public function getStorageClass(): string
    {
        return '';    
    }
    
    /**
     * A storage name tells the storage facade, what storage it should use
     * @return string
     */
	public function getStorageName(): string
	{
	    return '';
	}
	
	/**
	 * The storage id tells the storage the unique identification (like an int ID or a 
	 * timestamp)

	 * @return NULL
	 */
	public function getStorageID()
	{
	    return null;
	}
	
	protected function doPreCommit()
	{
	    if (!$this->initialized) {
	        if (isset($this->default) || $this->defaults_null) {
	            $this->value = $this->default;
	        } else {
	            throw new PropertyException(__("Commit of a not initialized property: ':name'",['name'=>$this->name]));
	        }
	        $this->initialized = true;
	    }	    
	}
	
	/**
	 * Checks if this property can store itself. If yes, create a storage and store
	 * the value
	 */
	protected function doCommit()
	{
	    if (empty($this->getStorageClass())) {
	        return;
	    }
	    $storage = Storage::createStorage($this);
	    if ($this->getStorageID()) {
	        $storage->updateToStorage($this->getStorageID());
	    } else {
	        $this->storageID = $storage->insertIntoStorage();
	    }
	}
	
	protected function doPostCommit()
	{
	    $this->shadow = $this->value;	    
	    $this->dirty = false;
	}
	
    /**
     * Commit the changes that where made since the last commit() or loading
     */
	public function commit() 
	{
	    $this->checkValidity();
	    if ($this->isCommitting()) {
	        return;
	    }
	    $this->setState('committing');
	    $this->doPreCommit();
		$this->doCommit();
		$this->doPostCommit();
		$this->setState('normal');
	}
	
	protected function doPreRollback()
	{
	    
	}
	
	protected function doRollback()
	{
	    $this->value = $this->shadow;
	}
	
	protected function doPostRollback()
	{
	    $this->dirty = false;	    
	}
	
    /**
     * Rollback the changes that were made this the last commit() or loading
     */
	public function rollback()
	{
	    $this->checkValidity();
	    if ($this->isCommitting()) {
	        return;
	    }
	    $this->setState('committing');
	    $this->doPreRollback();
		$this->doRollback();
		$this->doPostRollback();
		$this->setState('normal');		
	}
	
    /**
     * Checks via the validator if the value is valid for this property.
     * @param $value The value to test
     * @return bool: True if it's valid otherwise false
     */
	protected function validate($value) 
	{
		return $this->validator->validate($value);
	}
	
	// ================================== Loading ===========================================	
    
    /** 
     * Tries to load this property from the storage with the given id
     * 
     * @param unknown $id
     * @throws PropertyException
     */
    public function load($id)
    {
        if (empty($this->getStorageClass())) {
            throw new PropertyException("This class can't load itself from a storage");
        }
        $storage = Storage::createStorage($this);
        $storage->load($id);
        $this->loadFromStorage($storage);
    }
	
	final public function loadFromStorage(StorageBase $loader) 
	{
	    $this->checkValidity();
	    
	    $name = $this->getName();
        $this->doLoad($loader,$name);
	    $this->initialized = true; 
	    $this->dirty = false;
	}

	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\ORM\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function doLoad(StorageBase $loader, string $name) 
	{
	    $this->value = $loader->$name;
	}

	// ============================= Insert =========================================	
	/**
	 * Wird für jede Property aufgerufen, um den Wert in das Storage zu schreiben
	 */
	public function insertIntoStorage(StorageBase $storage) 
	{
	    $this->doInsert($storage,$this->getName());
	    $this->dirty = false;	    
	}
	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Speichermethoden zu verwenden
	 * @param \Sunhill\ORM\Storage\storage_insert $storage
	 * @param string $tablename
	 * @param string $name
	 */
	protected function doInsert(StorageBase $storage, string $name) 
	{
	    $storage->setEntity($name, $this->value);
	}
	
	public function updateToStorage(StorageBase $storage) 
	{
	    if ($this->dirty) {
            $diff = $this->getDiffArray(PD_KEEP);
    	    $this->doUpdate($storage,$this->getName());
    	    $this->dirty = false;
	    }
	}
	
	protected function doUpdate(StorageBase $storage, string $name) {
        $diff = $this->getDiffArray(PD_ID);
	    $storage->setEntity($name,$diff);	    
	}
	
    /**
     * Returns a Descriptor array with all static (unchangable) values of this property.
     * @return \Sunhill\basic\Utils\Descriptor The collection of values
     */
	public function getStaticAttributes() 
	{
	    $result = new Descriptor();
	    $result->class = $this->class;
	    $result->default = $this->default;
	    $result->defaults_null = $this->defaults_null;
	    $result->name = $this->name;
	    $result->read_only = $this->read_only;
	    $result->searchable = $this->searchable;
	    $result->type = $this->type;
	    foreach ($this->additional_fields as $key => $value) {
	        $result->$key = $value;
	    }
	    return $result;
	}
	
    /**
     * Completes the method Property->getStaticAttributes() with values that a volatile.
     * @return \Sunhill\basic\Utils\Descriptor The collection of values
     */
	public function getAllAttributes() 
	{
	    $result = $this->getStaticAttributes();
	    $result->value = $this->value;
	    $result->shadow = $this->shadow;
	    $result->dirty = $this->dirty;
	    return $result;
	}
}
