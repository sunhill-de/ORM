<?php
/**
 * @file PropertiesHaving.php
 * Defines the class PropertiesHaving. This is, as the name suggents, a class that has properties. 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-06-25
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: unknown
 * PSR-State: some type hints missing
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Search\query_builder;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\hookable;
use Sunhill\ORM\Facades\Classes;

/**
 * Basic class for all classes that have properties.
 * This class inherits from hookable
 * * - CONSTRUCTED
 * 
 * The class defines following hooks
 * - COMMITTING is called before a commit
 * - COMMITTED is called after a commit
 * - LOADING is called before loading
 * - LOADED is called after loading
 * - INSERTING is called before inserting
 * - INSERTED is called after inserting
 * - UPDATING is called before updating
 * - UPDATED is called after updating
 * - DELETING is called before deleting
 * - DELETED is called after deleting
 * 
 * The following hooks a defined via the properties
 * - PROPERTY_CHANGING
 * - PROPERTY_CHANGED
 *  
 * @author lokal
 */
class PopertiesHaving extends Hookable 
{
	
    protected $id;
    
    protected $state = 'normal';
    
    private $readonly = false;
    
    protected $properties;
    
    public static $object_infos = [
        'name'=>'propertieshaving',       // A repetition of static:$object_name @todo see above
        'table'=>'',     // A repitition of static:$table_name
        'name_s'=>'properties having',     // A human readable name in singular
        'name_p'=>'properties having',    // A human readable name in plural
        'description'=>'Baseclass of all other classes in the ORM system. An oo_object should\'t be initiated directly',
        'options'=>0,           // Reserved for later purposes
    ];
    /**
     * Constructor calles setupProperties()
     */
	public function __construct() 
    {
		parent::__construct();
		self::initializeProperties();
		$this->copyProperties();
	}
	
	protected function setupHooks() {
	    $this->addHook('COMMITTED','clear_dirty');
	}
	
	// ================================= ID-Handling =======================================
	/**
	 * Returns the current id of this object (or null, when this object wasn't stored yet) 
	 * @return int|null
	 */
	public function getID(): int 
    {
	    return $this->id;
	}
	
	/**
	 * Sets the ID for the current object
	 * @param Integer $id
	 */
	public function setID(int $id): null
    {
	    $this->id = $id;
	}
	
    /**
	 * Sets a new value for readonly
	 * @param bool $value
	 * @return \Sunhill\propertieshaving
	 */
	protected function setReadonly(bool $value): PropertiesHaving
    {
	    $this->readonly = $value;
	    return $this;
	}
	
	/**
	 * Returns the value for readonly
	 * @return bool
	 */
	protected function getReadonly(): bool
    {
	    return $this->readonly;
	}
	
// ============================== State-Handling ===========================================	
	
    /**
     * Sets the current state of this object
     * @param $state string the new state
     */
	protected function setState(string $state): PropertiesHaving 
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
     * Raises an exception if the object is invalid
     */
    protected function checkValidity(): null 
    {
	    if ($this->isInvalid()) {
	        throw new PropertiesHavingException(__('Invalided object called.'));
	    }
	}
// ==================================== Loading =========================================
	
    /**
     * Loads the object with the id $id from the storage
     * @param $id int The id of the object to load
     * @returns propertieshaving Reference to self
     * @throws PropertiesHavingException If the object is invalid
     */
	public function load(int $id): PropertiesHaving 
    {
	    $this->checkValidity(); // Is this object inavlid?
	    
        if ($result = $this->checkCache($id)) { // Is this object already in the cache
	        $this->setState('invalid'); // If yes, this object is invalid
	        return $result; // Return the cache instead
	    }
	    
        $this->insertCache($id); // Insert into cache
	    $this->setID($id);       
	    
        $this->checkForHook('LOADING','default',array($id));
	    $this->doLoad();
	    $this->cleanProperties();
	    $this->checkForHook('LOADED','default',array($id));
	    
        return $this;
	}
	
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
	protected function insert_cache(int $id): null 
    {
	}
	
    /**
     * Does the loading (has to be overwritten)
     */
	protected function do_load() 
    {
	}
	
// ===================================== Committing =======================================
	/**
     * Stores the object into the storage
     */
    public function commit($caller=null) 
    {
	    $this->checkValidity();
    	if (!$this->isCommitting()) { // Guard to protect from circular calls
	        $this->setState('committing');
	        $this->checkForHook('COMMITTING');
	        if ($this->getID()) {
	            $this->update(); // This object is already in a storage
	        } else {
	            $this->insert(); // This object is new
	        }
	        $this->checkForHook('COMMITTED');
	        $this->setState('normal');
	    }
	    return;
	}

    /**
     * Returns if one of the properties is modified since the last commit(), rollback() or load()
     * @returns bool
     */
	protected function getDirty() 
    {
	    $dirty_properties = $this->getPropertiesWithFeature('',true);
	    return (!empty($dirty_properties));	    
	}
	
	protected function doRecommit() 
    {
	    
	}
	
// ====================================== Updating ========================================	
	/**
     * Checks for hooks and calls do_update
     */
    protected function update() 
    {
	    $this->checkForHook('UPDATING');
	    $this->doUpdate();
	    $this->checkForHook('UPDATED');
	}

    /**
     * Does the update work
     */
	protected function doUpdate() 
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
     * Checks for hooks and calls do_insert
     */
    protected function insert() 
    {
	    $this->checkForHook('INSERTING');
	    $this->doInsert();
	    $this->checkForHook('INSERTED');
	}

	/**
	 * Does the insert work
	 * @param bool $recommit
	 */
	protected function doInsert() 
    {
	   // has to be overwritten in child objects 
	}
	
	// ====================================== Deleting ==========================================
	/**
     * Checks for hooks and calls do_delete and clears the cache
     */
    public function delete() 
    {
	    $this->checkForHook('DELETING');
	    $this->doDelete();
	    $this->checkForHook('DELETED');
	    $this->clearCacheEntry();
	}
	
    /**
     * Does the delete work
     */
	protected function doDelete() 
    {
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
	        //$this->properties[$name]->setClass(get_class($this));
	        $this->properties[$name]->setOwner($this);
	    }
	}

    /**
     * Undirties all properties 
     */
	public function clean_properties() 
    {
	    foreach ($this->properties as $property) {
	        $property->set_dirty(false);
	    }
	}
	
    /**
     * Searches for a property with the given name. If there is one, return its value. If not pass it to the parent __get method
     * @param $name string The name of the unknown member variable
     */
	public function &__get($name) 
    {
	    if (isset($this->properties[$name])) {
	        $this->check_for_hook('GET',$name,null);
	        return $this->properties[$name]->get_value();
	    } else {
	        return parent::__get($name);
	    }
	}

    /**
     * Searches for a property with the given name. If there is one, set its value. if not call handle_unknown_property()
     * @param $name string The name of the unknown member variable
     * @param $value void The valie for this member variable
     */
	public function __set($name,$value) 
    {
	    if (isset($this->properties[$name])) {
	        if ($this->get_readonly()) {
	            throw new PropertiesHavingException("Property '$name' was changed in readonly state.");
	        } else {
	            $this->properties[$name]->set_value($value);
	            $this->check_for_hook('SET',$name,array(
	                'from'=>$this->properties[$name]->get_old_value(),
	                'to'=>$value));
	            if (!$this->properties[$name]->is_simple()) {
	                $this->check_for_hook('EXTERNAL',$name,array('to'=>$value,'from'=>$this->properties[$name]->get_old_value()));
	            }
	            if ($this->properties[$name]->get_dirty()) {
	                $this->check_for_hook('FIELDCHANGE',$name,array(
	                    'from'=>$this->properties[$name]->get_old_value(),
	                    'to'=>$this->properties[$name]->get_value()));
	            }
	        }
	    } else if (!$this->handle_unknown_property($name,$value)){
	        throw new PropertiesHavingException("Unknown property '$name'");
	    }
	}
	
	/**
	 * Tries to handle an unknown property. If it can't be handled return false, then an exception will be raised
	 * @param unknown $name The Name of the property
	 * @param unknown $value The value of the property
	 * @return boolean
	 */
	protected function handle_unknown_property($name,$value) 
    {
	   return false;    
	}
	
	/**
	 * Returns the property object with the given name or raises an exception if there is no such property
	 * @param string $name Name of the property
	 * @return oo_property
	 */
	public function get_property(string $name,bool $return_null=false) 
    {
	    if (!isset($this->properties[$name])) {
	        if ($return_null) {
	            return null;
	        }
	        throw new PropertiesHavingException("Unknown property '$name'");
	    }
	    return $this->properties[$name];
	}
	
	/**
	 * Liefert alle Properties zurück, die ein bestimmtes Feature haben
	 * @param string $feature, wenn ungleich null, werden nur die Properties zurückgegeben, die ein bestimmtes Feature haben
     * @param bool $dirty, wenn true, dann nur dirty-Properties, wenn false dann nur undirty, wenn null dann alle
     * @param string $group, wenn nicht null, dann werden die Properties nach dem Ergebnis von get_$group gruppiert
	 * @return unknown[]
	 */
	public function getPropertiesWithFeature(string $feature = '', $dirty = null, $group = null) 
    {
	    $result = array();
	    if (isset($group)) {
	        $group = 'get_'.$group;
	    }
	    foreach ($this->properties as $name => $property) {
	        // Als erstes auswerten, ob $dirty berücksichtigt werden soll
	        if (isset($dirty)) {
	            if ($dirty && (!$property->get_dirty())) {
	                continue;
	            } else if (!$dirty && ($property->get_dirty())) {
	                continue;
	            }
	        }
	        if (empty($feature)) { // Gibt es Features zu berücksichgigen
	            if (isset($group)) { // Soll gruppiert werden
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
	               if (isset($group)) { // Soll gruppiert werden
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

	protected function dynamicAddProperty(string $name,string $type) 
    {
	    $property = static::createProperty($name, $type);
	    $property->setOwner($this);
	    $this->properties[$name] = $property;
	    return $property;	    
	}
	// ========================== Static methods ================================
	
	protected static $property_definitions;
	
	public static function initializeProperties(): null 
    {
 	       static::$property_definitions = array();
	       static::setup_properties();
	}
	
	protected static function setupProperties(): null 
    {
	    
	}

	private static function getCallingClass(): string 
    {
	    $caller = debug_backtrace();
	    return $caller[4]['class'];
	}
	
	protected static function createProperty(string $name, string $type, PropertiesHaving|null $class = null) 
    {
	    $property_name = '\Sunhill\ORM\Properties\oo_property_'.$type;
	    $property = new $property_name();
	    $property->setName($name);
	    $property->setType($type);
	    $property->setClass(is_null($class)?Classes::getClassName(self::getCallingClass()):$class);
	    $property->initialize();
	    return $property;
	}
	
	protected static function addProperty(string $name,string $type) 
    {
	    $property = static::createProperty($name, $type);
	    static::$property_definitions[$name] = $property;
	    return $property;
	}
	
	public static function getPropertyObject(string $name) 
    {
	    static::initializeProperties();
	    if (isset(static::$property_definitions[$name])) {
	        return static::$property_definitions[$name];
	    } else {
	        return null;
	    }
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
	    if (isset($group)) {
	        $group = 'get_'.$group;
	    }
	    if (empty(static::$property_definitions)) {
	        static::setupProperties();
	        if (empty(static::$property_definitions)) {
	            return $result;
	        }
	    }
	    foreach (static::$property_definitions as $name => $property) 
        {
	        if (empty($feature)) { // Are there features
	            if (isset($group)) { // Should we group
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
	            if ($property->has_feature($feature)) {
	                if (isset($group)) { // Should we group
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
	
	public static function getPropertyInfo(string $name) 
    {
	    static::initialize_properties();
	    return static::$property_definitions[$name];
	}
	
	public static function search() {
	     $query = new query_builder();
	     $query->set_calling_class(get_called_class());
	     return $query;
	}
	
	protected static function timestamp($name) {
	    $property = self::addProperty($name, 'timestamp');
	    return $property;
	}
	
	protected static function integer($name) 
    {
	    $property = self::addProperty($name, 'integer');
	    return $property;
	}
	
	protected static function varchar($name) 
    {
	    $property = self::addProperty($name, 'varchar');
	    return $property;
	}
	
	protected static function object($name) {
	    $property = self::addProperty($name, 'object');
	    return $property;
	}
	
	protected static function text($name) 
    {
	    $property = self::addProperty($name, 'text');
	    return $property;
	}
	
	protected static function enum($name) 
    {
	    $property = self::addProperty($name, 'enum');
	    return $property;
	}
	
	protected static function datetime($name) 
    {
	    $property = self::addProperty($name, 'datetime');
	    return $property;
	}
	
	protected static function date($name) 
    {
	    $property = self::addProperty($name, 'date');
	    return $property;
	}
	
	protected static function time($name) 
    {
	    $property = self::addProperty($name, 'time');
	    return $property;
	}
	
	protected static function float($name) 
    {
	    $property = self::addProperty($name, 'float');
	    return $property;
	}
	
	protected static function arrayOfStrings($name) 
    {
	    $property = self::addProperty($name, 'array_of_strings');
	    return $property;
	}
	
	protected static function arrayOfObjects($name) 
    {
	    $property = self::addProperty($name, 'array_of_objects');
	    return $property;
	}
	
    /**
     * Defines a calculated property with the name $name
     * @param $name The name of this property
     * @see Sunhill/ORM/Properties/
     */     
	protected static function calculated($name) 
    {
	    $property = self::addProperty($name, 'calculated');
	    return $property;
	}
	
}
