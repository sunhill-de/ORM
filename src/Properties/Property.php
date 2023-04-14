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
use Sunhill\ORM\PropertiesHaving;
use Sunhill\ORM\Storage\StorageBase;

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
    
    /**
     * This array store special "features" of this property so properties can be filtered by this featured.
     * To check if a certain feature is set the method Property->hasFeature() is used.
     * @var array
     */
    protected $features = array();
    
    /**
     * This field stores the owner of this property. It points to an descendand of PropertiesHaving 
     * Property->getOwner() reads, Property->setOwner() writes
     * @var \Sunhill\ORM\PropertiesHaving
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
	 * The shadow value of this property. This is the value after the last Property->commit()
     * It is used for rollback and creation of the diff array (Property->getDiffArray())
	 * @var void
	 */
	protected $shadow;
	
	/**
	 * The type of this property. Is set by the property itself and can't (or shouldn't) be changed
	 * @var string
	 */
	protected $type;
	
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
	 * Stores the hooks of this property
	 * @var array
	 */
	protected $hooks = array();
	
    /**
     * Stores the class of the property
     * @var string
     */
	protected $class;
	
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
		if ($this->is_array()) {
			$this->value = array();
		}
		$this->initialize();
		$this->initValidator();
	}
	
	/**
	 * Extends the property with the possibility to deal with additional getters and setters
	 * @param unknown $method
	 * @param unknown $params
	 * @return mixed|NULL|\Sunhill\ORM\Properties\Property
	 */
	public function __call($method, $params) 
	{
	    if (substr($method,0,4) == 'get_') {
	        $name = substr($method,4);
	        if (isset($this->additional_fields[$name])) {
	            return $this->additional_fields[$name];
	        } else {
	            return null;
	        }
	    } else if (substr($method,0,4) == 'set_') {
	        $name = substr($method,4);
	        $this->additional_fields[$name] = $params[0];
	        return $this;
	    } else if (substr($method,0,3) == 'get') {
	        $name = strtolower(substr($method,3));
	        if (isset($this->additional_fields[$name])) {
	            return $this->additional_fields[$name];
	        } else {
	            return null;
	        }
	    } else if (substr($method,0,3) == 'set') {
	        $name = substr($method,3);
	        $this->additional_fields[$name] = $params[0];
	        return $this;
	    }
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

// =========================== Setter and getter ========================================	
    /**
     * sets the field Property->owner
     * @param $owner a class of PropertiesHaving
     * @return Property a reference to this to make setter chains possible
     */
    public function setOwner($owner)
    {
	    $this->owner = $owner;
	    return $this;	    
    }

    /**
     * Returns the value of the owner field
     * @return PropertiesHaving
     */
    public function getOwner()
    {
	    return $this->owner;
    }
	
    /**
     * sets the field Property->name
     * @param $name The name of the property
     * @return Property a reference to this to make setter chains possible
     */
    public function setName(string $name)
    {
	    $this->name = $name;
	    return $this;
    }
    
    /**
     * Returns the name of this property
     */
    public function getName(): ?string 
    {
	    return $this->name;
    }
	
    /**
     * sets the field Property->type
     * @param $type The type of the property
     * @return Property a reference to this to make setter chains possible
     */
    public function setType(string $type)
    {
	    $this->type = $type;
	    return $this;
    }
	
    public function getType(): string
    {
	    return $this->type;
    }
	
    /**
     * sets the field Property->default (and perhaps Property->defaults_null too)
     * 
     * @return Property a reference to this to make setter chains possible
     */
    public function setDefault($default) 
    {
	    if (!isset($default)) {
	        $this->defaults_null = true;
	    }
	    $this->default = $default;
	    return $this;
    }
	
    public function getDefault()
    {
	    return $this->default;
    }
	
    public function getDefaultsNull()
    {
        return $this->defaults_null;    
    }
    
    public function setClass(string $class) 
    {
	    $this->class = $class;
	    return $this;
    }
	
    public function getClass() 
    {
	    return $this->class;
    }
	
    public function setReadonly(bool $value) 
    {
	    $this->read_only = $value;
	    return $this;
    }
	
    public function getReadonly(): bool
    {
	    return $this->read_only;
    }
	
    public function searchable()
    {
	    $this->searchable = true;
	    return $this;
    }
	
    public function getSearchable() 
    {
	    return $this->searchable;
    }
	
	
// ============================== Value Handling =====================================	
	/**
	 * Writes the value of this property
	 * @param unknown $value
	 * @param unknown $index
	 * @throws PropertyException
	 * @return \Sunhill\ORM\Properties\Property
	 */
	final public function setValue($value, $index = null)
	{
		if ($this->read_only) {
			throw new PropertyException(__("Write to a read only property."));
		}
		
		// Prüfen, ob sich der Wert überhaupt ändert
		if ($this->initialized && ($value === $this->value)) {
    		return $this;
		}
        	$oldvalue = $this->value;
        	$this->valueChanging($oldvalue,$value);
		if (!$this->dirty) {
		    $this->shadow = $this->value;
		    $this->dirty = true;
		}
		
		if (is_null($index)) {
		      $this->doSetValue((is_null($value)?null:$this->validate($value)));
		} else {
		    $this->doSetIndexedValue($index,(is_null($value)?null:$this->validate($value)));
		}
		    
		$this->initialized = true;
		$this->valueChanged($oldvalue,$this->value);
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
	
	/**
	 * If there is an owner this method calls its checkForHook method
	 * @param unknown $action
	 * @param unknown $subaction
	 * @param unknown $info
	 */
	protected function checkOwnerHook($action, $subaction, $info) 
	{
	    if (!empty($this->owner)) {
	        $this->owner->checkForHook($action, $subaction, $info);
	    }
	}
	
	/**
	 * Prüft auf Hooks, die vor dem Ändern eines Wertes aufgerufen werden sollen
	 * @param unknown $from Alter Wert der Property
	 * @param unknown $to Neuer Wert der Property
	 */
	protected function valueChanging($from, $to) 
	{
	    $this->checkOwnerHook('PROPERTY_CHANGING',$this->getName(),array('FROM'=>$from,'TO'=>$to));
	}
	
	/**
	 * Prüft auf Hooks, die nach dem Ändern eines Wertes aufgerufen werden sollen
	 * @param unknown $from Alter Wert der Property
	 * @param unknown $to Neuer Wert der Property
	 */
	protected function valueChanged($from, $to) 
	{
	    $this->checkOwnerHook('PROPERTY_CHANGED',$this->getName(),array('FROM'=>$from,'TO'=>$to));
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
		if ($this->is_array()) {
		        return $this;
		} else {
		        return $this->doGetValue();
		}
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
     * Commit the changes that where made since the last commit() or loading
     */
	public function commit() 
	{
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;	
			} else {
				throw new PropertyException(__("Commit of a not initialized property: ':name'",['name'=>$this->name]));
			}
		}
		$this->dirty = false;
		$this->shadow = $this->value;
	}
	
    /**
     * Rollback the changes that were made this the last commit() or loading
     */
	public function rollback()
	{
		$this->dirty = false;
		$this->value = $this->shadow;
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
	
    /**
     * Checks if this property is an array 
     * @return bool: True if it's an array otherwise false
     */
	public function is_array() 
	{
		return $this->hasFeature('array');
	}
	
    /**
     * Checks if this property is a simple property 
     * @return bool: True if it's a simple property otherwise false
     */
	public function is_simple() 
	{
		return $this->hasFeature('simple');
	}
	
    /**
     * Tests if the property has the given feature
     * @return bool: True if it has the feature otherwise false
     */
	public function hasFeature(string $test) 
	{
	    return in_array($test,$this->features);
	}
	
	public function deleting(StorageBase $storage) 
	{
	   // Does nothing by default	    
	}
	
	public function deleted(StorageBase $storage) 
	{
	   // Does nothing by default	    
	}
	
	public function delete($storage) {
	    
	}
	
	// ================================== Loading ===========================================	
	/**
	 * Wird für jede Property aufgerufen, um den Wert aus dem Storage zu lesen
	 * Ruft wiederrum die überschreibbare Methode do_load auf, die property-Individuelle Dinge erledigen kann
	 * @param \Sunhill\ORM\Storage\storage_load $loader
	 */
	final public function load(StorageBase $loader) 
	{
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
	protected function doLoad(StorageBase $loader, $name) 
	{
	    $this->value = $loader->$name;
	}

	/**
	 * Wird aufgerufen, bevor das Property geladen wird
	 * @param \Sunhill\ORM\Storage\StorageBase $storage
	 */
	public function loading(StorageBase $storage) 
	{
	   // Does nothing by default
	}
	
	/**
	 * Wird aufgerufen, nachdem das Property geladen ist
	 * @param \Sunhill\ORM\Storage\StorageBase $storage
	 */
	public function loaded(StorageBase $storage) 
	{
	   // Does nothing by default
	}
	
	// ============================= Insert =========================================	
	/**
	 * Wird für jede Property aufgerufen, um den Wert in das Storage zu schreiben
	 */
	public function insert(StorageBase $storage) 
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
	
    /**
     * Wird vor dem Einfügen aufgerufen
     * @param \Sunhill\ORM\Storage\StorageBase $storage
     */
	public function inserting(StorageBase $storage) 
	{
	   // Does nothing by default
	}
	
	/**
	 * Wird nach dem Einfügen aufgerufen
	 * @param \Sunhill\ORM\Storage\StorageBase $storage
	 */
	public function inserted(StorageBase $storage) 
	{
	   // Does nothing by default
	}

// ================================= Update ====================================	
	public function update(StorageBase $storage) 
	{
	    if ($this->dirty) {
            $diff = $this->getDiffArray(PD_KEEP);
	        $this->getOwner()->checkForHook('UPDATING_PROPERTY',$this->getName(),$diff);
    	    $this->doUpdate($storage,$this->getName());
    	    $this->getOwner()->checkForHook('UPDATED_PROPERTY',$this->getName(),$diff);    	    
    	    $this->dirty = false;
	    }
	}
	
	protected function doUpdate(StorageBase $storage, string $name) {
        $diff = $this->getDiffArray(PD_ID);
	    $storage->setEntity($name,$diff);	    
	}
	
    /**
     * Is called before an update
     * @param \Sunhill\ORM\Storage\StorageBase $storage
     */
	public function updating(StorageBase $storage) 
	{
	    // Does nothis by default
	}
	
    /**
     * Is called after an update
     * @param \Sunhill\ORM\Storage\StorageBase $storage
     */
	public function updated(StorageBase $storage) 
	{
	   // Does nothing by default
	}
	
    /**
     * Adds an hook for this property
     */
	public function addHook($action,$hook,$subaction,$target) 
	{
	   $this->hooks[] = ['action'=>$action,'hook'=>$hook,'subaction'=>$subaction,'target'=>$target];    
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
	    $result->features = $this->features;
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
