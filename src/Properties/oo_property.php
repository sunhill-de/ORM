<?php

/**
 * @file oo_property.php
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
use Sunhill\Basic\Utils\descriptor;
use Sunhill\ORM\ORMException;
use Sunhill\Basic\loggable;
use Sunhill\ORM\propertyhaving;

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
 * a basic exception class that deal with properties
 * @author lokal
 *
 */
class PropertyException extends ORMException {}

/**
 * An exception that is raised, if a reference is assigned an invalid value
 * @author lokal
 */
class InvalidValueException extends PropertyException {}

/**
 * A basic class for properties. 
 * @author lokal
 *
 */
class oo_property extends loggable {
	
    /**
     * Properties get the possibility to add additinal fields (like property->set_additional)
     */
    private $additional_fields = [];
    
    /**
     * This array store special "features" of this property so properties can be filtered by this featured.
     * To check if a certain feature is set the method oo_property->has_feature() is used.
     * @var array
     */
    protected $features = array();
    
    /**
     * This field stores the owner of this property. It points to an descendand of propertieshaving 
     * oo_property->get_owner() reads, oo_property->set_owner() writes
     * @var \Sunhill\ORM\propertieshaving
     */
	protected $owner;
	
    /**
     * The name of this property
     * oo_property->get_name() reads, oo_property->set_name() writes
     * @var string
     */
	protected $name;
	
	/**
	 * The value of this property
	 * @var void
	 */
	protected $value;
	
	/**
	 * The shadow value of this property. This is the value after the last oo_property->commit()
     * It is used for rollback and creation of the diff array (oo_property->get_diff_array())
	 * @var void
	 */
	protected $shadow;
	
	/**
	 * The type of this property. Is set by the property itself and can't (or shouldn't) be changed
	 * @var string
	 */
	protected $type;
	
	/**
	 * The default value for the value field. In combination with oo_property->defaults_null this default value 
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
     * commit. If true than it was changed. An access should be performed via oo_property->get_dirty() and
     * oo_property->set_dirty().
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
	protected $validator_name = 'validator_base';
	
	/**
     * Stores the validator object
     * @var \Sunhill\ORM\Validators\validator_base
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
	public function __construct() {
		$this->dirty = false;
		$this->defaults_null = false;
		$this->read_only = false;
		if ($this->is_array()) {
			$this->value = array();
		}
		$this->initialize();
		$this->init_validator();
	}
	
	/**
	 * Extends the property with the possibility to deal with additional getters and setters
	 * @param unknown $method
	 * @param unknown $params
	 * @return mixed|NULL|\Sunhill\ORM\Properties\oo_property
	 */
	public function __call($method,$params) {
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
	    }
	}
	
	/**
	 * A method to provide the possibility to initialize this property. Is not the same as
     * setting initialized to true.
	 */
	public function initialize() {
	}
	
	/**
	 * Initializes the validator 
	 * @throws PropertyException if the validator class dosn't exist
	 */
	protected function init_validator() {
	    $validator_name = "\\Sunhill\\ORM\\Validators\\".$this->validator_name;
	    if (!class_exists($validator_name)) {
	        throw new PropertyException("Unknown validator '".$this->validator_name."' called.");
	    }
	    $this->validator = new $validator_name();    
	}

// =========================== Setter and getter ========================================	
    /**
     * sets the field oo_property->owner
     * @param $owner a class of propertyhaving
     * @return oo_property a reference to this to make setter chains possible
     */
    public function set_owner($owner) {
	    $this->owner = $owner;
	    return $this;	    
	}

	public function get_owner() {
	    return $this->owner;
	}
	
    /**
     * sets the field oo_property->name
     * @param $name The name of the property
     * @return oo_property a reference to this to make setter chains possible
     */
	public function set_name(string $name) {
		$this->name = $name;
		return $this;
	}
	 
	public function get_name() {
		return $this->name;
	}
	
    /**
     * sets the field oo_property->type
     * @param $type The type of the property
     * @return oo_property a reference to this to make setter chains possible
     */
	public function set_type(string $type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
	
    /**
     * sets the field oo_property->default (and perhaps oo_property->defaults_null too)
     * 
     * @return oo_property a reference to this to make setter chains possible
     */
	public function set_default($default) {
	    if (!isset($default)) {
	        $this->defaults_null = true;
	    }
	    $this->default = $default;
	    return $this;
	}
	
	public function get_default() {
	    return $this->default;
	}
	
	public function set_class(string $class) {
	    $this->class = $class;
	    return $this;
	}
	
	public function get_class() {
	    return $this->class;
	}
	
	public function set_readonly($value) {
	    $this->read_only = $value;
	    return $this;
	}
	
	public function get_readonly() {
	    return $this->read_only;
	}
	
	public function searchable() {
	    $this->searchable = true;
	    return $this;
	}
	
	public function get_searchable() {
	    return $this->searchable;
	}
	
	
// ============================== Value Handling =====================================	
	/**
	 * Greift schreibend auf den Wert von $value zu. Darf nicht überschrieben werden.
	 * @param unknown $value
	 * @param unknown $index
	 * @throws PropertyException
	 * @return \Sunhill\ORM\Properties\oo_property
	 */
	final public function set_value($value,$index=null) {
		if ($this->read_only) {
			throw new PropertyException("Write to a read only property.");
		}
		
		// Prüfen, ob sich der Wert überhaupt ändert
		if ($this->initialized && ($value === $this->value)) {
    		return $this;
		}
        $oldvalue = $this->value;
        $this->value_changing($oldvalue,$value);
		if (!$this->dirty) {
		    $this->shadow = $this->value;
		    $this->dirty = true;
		}
		
		if (is_null($index)) {
		      $this->do_set_value((is_null($value)?null:$this->validate($value)));
		} else {
		    $this->do_set_indexed_value($index,(is_null($value)?null:$this->validate($value)));
		}
		    
		$this->initialized = true;
		$this->value_changed($oldvalue,$this->value);
		return $this;
	}

	/**
	 * Schreibt den neuen Wert nach $value
	 * @param unknown $value
	 */
	protected function do_set_value($value) {
	    $this->value = $value;
	}
	
	/**
	 * Prüft, ob ein Owner gesetzt ist. Wenn ja wird dessen Methode check_for_hook aufgerufen
	 * @param unknown $action
	 * @param unknown $subaction
	 * @param unknown $info
	 */
	protected function check_owner_hook($action,$subaction,$info) {
	    if (!empty($this->owner)) {
	        $this->owner->check_for_hook($action,$subaction,$info);
	    }
	}
	
	/**
	 * Prüft auf Hooks, die vor dem Ändern eines Wertes aufgerufen werden sollen
	 * @param unknown $from Alter Wert der Property
	 * @param unknown $to Neuer Wert der Property
	 */
	protected function value_changing($from,$to) {
	    $this->check_owner_hook('PROPERTY_CHANGING',$this->get_name(),array('FROM'=>$from,'TO'=>$to));
	}
	
	/**
	 * Prüft auf Hooks, die nach dem Ändern eines Wertes aufgerufen werden sollen
	 * @param unknown $from Alter Wert der Property
	 * @param unknown $to Neuer Wert der Property
	 */
	protected function value_changed($from,$to) {
	    $this->check_owner_hook('PROPERTY_CHANGED',$this->get_name(),array('FROM'=>$from,'TO'=>$to));
	}
	
	final public function &get_value() {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;
				$this->shadow = $this->default;
				$this->initialized = true;
			} else {
			    if (!$this->initialize_value()) {
			         throw new PropertyException("Read of a not initialized property: '".$this->name."'");
			    }
			}
		}
		if ($this->is_array()) {
		        return $this;
		} else {
		        return $this->do_get_value();
		}
	}

	/**
	 * Hier kann man noch zusätzlich versuchen, einem uninitialisierten Wert einen solchen noch zuzuweisen (z.B. Calculate-Felder)
	 * @return boolean
	 */
	protected function initialize_value() {
	    return false;
	}
	
	protected function &do_get_value() {
	    return $this->value;    
	}
	
	protected function &do_get_indexed_value($index) {
	    return $this->value[$index];
	}
	
    /**
     * Returns the value of the shadow field 
     * @return void: The value of oo_property->shadow
     */
	public function get_old_value() {
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
	public function get_diff_array(int $type=PD_VALUE) {
	    return array('FROM'=>$this->get_diff_entry($this->shadow,$type),
	                 'TO'=>$this->get_diff_entry($this->value,$type));
	}
	
	/**
	 * Da der Typ bei den meisten Properties ignoriert wird, kann eine abgeleitete Property diese
	 * Methode überschreiben, um den Praemeter $type zu respektieren.
	 * @param unknown $entry
	 * @param int $type
	 * @return unknown
	 */
	protected function get_diff_entry($entry,int $type) {
	    return $entry;
	}
//========================== Dirtyness ===============================================	
	
    /**
     * Tests, if the property is dirty
     * @return bool: True if it is dirty otherwise false 
     */
    public function get_dirty() {
		return $this->dirty;	
	}
	
    /**
     * Sets the value of dirty to $value
     * @param bool $value The new value of dirty
     */
    public function set_dirty(bool $value) {
		$this->dirty = $value;
	}
	
    /**
     * Commit the changes that where made since the last commit() or loading
     */
	public function commit() {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;	
			} else {
				throw new PropertyException("Commit of a not initialized property: '".$this->name."'");
			}
		}
		$this->dirty = false;
		$this->shadow = $this->value;
	}
	
    /**
     * Rollback the changes that were made this the last commit() or loading
     */
	public function rollback() {
		$this->dirty = false;
		$this->value = $this->shadow;
	}
	
    /**
     * Checks via the validator if the value is valid for this property.
     * @param $value The value to test
     * @return bool: True if it's valid otherwise false
     */
	protected function validate($value) {
		return $this->validator->validate($value);
	}
	
    /**
     * Checks if this property is an array 
     * @return bool: True if it's an array otherwise false
     */
	public function is_array() {
		return $this->has_feature('array');
	}
	
    /**
     * Checks if this property is a simple property 
     * @return bool: True if it's a simple property otherwise false
     */
	public function is_simple() {
		return $this->has_feature('simple');
	}
	
    /**
     * Tests if the property has the given feature
     * @return bool: True if it has the feature otherwise false
     */
	public function has_feature(string $test) {
	    return in_array($test,$this->features);
	}
	
	public function deleting(\Sunhill\ORM\Storage\storage_base $storage) {
	   // Does nothing by default	    
	}
	
	public function deleted(\Sunhill\ORM\Storage\storage_base $storage) {
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
	final public function load(\Sunhill\ORM\Storage\storage_base $loader) {
	    $name = $this->get_name();
        $this->do_load($loader,$name);
	    $this->initialized = true; 
	    $this->dirty = false;
	}

	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\ORM\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function do_load(\Sunhill\ORM\Storage\storage_base $loader,$name) {
	    $this->value = $loader->$name;
	}

	/**
	 * Wird aufgerufen, bevor das Property geladen wird
	 * @param \Sunhill\ORM\Storage\storage_base $storage
	 */
	public function loading(\Sunhill\ORM\Storage\storage_base $storage) {
	   // Does nothing by default
	}
	
	/**
	 * Wird aufgerufen, nachdem das Property geladen ist
	 * @param \Sunhill\ORM\Storage\storage_base $storage
	 */
	public function loaded(\Sunhill\ORM\Storage\storage_base $storage) {
	   // Does nothing by default
	}
	
	// ============================= Insert =========================================	
	/**
	 * Wird für jede Property aufgerufen, um den Wert in das Storage zu schreiben
	 */
	public function insert(\Sunhill\ORM\Storage\storage_base $storage) {
	    $this->do_insert($storage,$this->get_name());
	    $this->dirty = false;	    
	}
	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Speichermethoden zu verwenden
	 * @param \Sunhill\ORM\Storage\storage_insert $storage
	 * @param string $tablename
	 * @param string $name
	 */
	protected function do_insert(\Sunhill\ORM\Storage\storage_base $storage,string $name) {
	    $storage->set_entity($name, $this->value);
	}
	
    /**
     * Wird vor dem Einfügen aufgerufen
     * @param \Sunhill\ORM\Storage\storage_base $storage
     */
	public function inserting(\Sunhill\ORM\Storage\storage_base $storage) {
	   // Does nothing by default
	}
	
	/**
	 * Wird nach dem Einfügen aufgerufen
	 * @param \Sunhill\ORM\Storage\storage_base $storage
	 */
	public function inserted(\Sunhill\ORM\Storage\storage_base $storage) {
	   // Does nothing by default
	}

// ================================= Update ====================================	
	public function update(\Sunhill\ORM\Storage\storage_base $storage) {
	    if ($this->dirty) {
            $diff = $this->get_diff_array(PD_KEEP);
	        $this->get_owner()->check_for_hook('UPDATING_PROPERTY',$this->get_name(),$diff);
    	    $this->do_update($storage,$this->get_name());
    	    $this->get_owner()->check_for_hook('UPDATED_PROPERTY',$this->get_name(),$diff);    	    
    	    $this->dirty = false;
	    }
	}
	
	protected function do_update(\Sunhill\ORM\Storage\storage_base $storage,string $name) {
        $diff = $this->get_diff_array(PD_ID);
	    $storage->set_entity($name,$diff);	    
	}
	
    /**
     * Is called before an update
     * @param \Sunhill\ORM\Storage\storage_base $storage
     */
	public function updating(\Sunhill\ORM\Storage\storage_base $storage) {
	    // Does nothis by default
	}
	
    /**
     * Is called after an update
     * @param \Sunhill\ORM\Storage\storage_base $storage
     */
	public function updated(\Sunhill\ORM\Storage\storage_base $storage) {
	   // Does nothing by default
	}
	
    /**
     * Adds an hook for this property
     */
	public function add_hook($action,$hook,$subaction,$target) {
	   $this->hooks[] = ['action'=>$action,'hook'=>$hook,'subaction'=>$subaction,'target'=>$target];    
	}

    /**
     * Returns a descriptor array with all static (unchangable) values of this property.
     * @return \Sunhill\basic\Utils\descriptor The collection of values
     */
	public function get_static_attributes() {
	    $result = new descriptor();
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
     * Completes the mthod oo_property->get_static_attributes() with values that a volatile.
     * @return \Sunhill\basic\Utils\descriptor The collection of values
     */
	public function get_all_attributes() {
	    $result = $this->get_static_attributes();
	    $result->value = $this->value;
	    $result->shadow = $this->shadow;
	    $result->dirty = $this->dirty;
	    return $result;
	}
}
