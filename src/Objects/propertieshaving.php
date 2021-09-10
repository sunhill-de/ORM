<?php
/**
 * @file propertieshaving.php
 * Defines the class propertyhaving. This is, as the name suggents, a class that has properties. 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-06-25
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: unknown
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
 * Die Klasse definiert folgende Hooks:
 * - COMMITTING Wird vor dem Commit aufgerufen
 * - COMMITTED Wird nach dem Commit aufgerufen 
 * - LOADING Wird vor dem Laden aufgerufen
 * - LOADED Wird nach dem Laden aufgerufen
 * - INSERTING Wird vor dem Einfügen aufgerufen
 * - INSERTED Wird nach dem Einfügen aufgerufen
 * - UPDATING Wird vor dem Update aufgerufen
 * - UPDATED Wird nach dem Update aufgerufen
 * - DELETING Wird vor dem Löschen aufgerufen
 * - DELETED Wird nach dem Löschen aufgerufen
 * 
 * Über die Properties werden folgende Hooks definiert:
 * - PROPERTY_CHANGING
 * - PROPERTY_CHANGED
 *  
 * @author lokal
 */
class propertieshaving extends hookable {
	
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
     * Konstruktur, ruft nur zusätzlich setup_properties auf
     */
	public function __construct() {
		parent::__construct();
		self::initialize_properties();
		$this->copy_properties();
	}
	
	protected function setup_hooks() {
	    $this->add_hook('COMMITTED','clear_dirty');
	}
	
	// ================================= ID-Handling =======================================
	/**
	 * Returns the current id of this object (or null, when this object wasn't stored yet) 
	 * @return int|null
	 */
	public function get_id() {
	    return $this->id;
	}
	
	/**
	 * Sets the ID for the current object
	 * @param Integer $id
	 */
	public function set_id($id) {
	    $this->id = $id;
	}
	/**
	 * Sets a new value for readonly
	 * @param bool $value
	 * @return \Sunhill\propertieshaving
	 */
	protected function set_readonly(bool $value) {
	    $this->readonly = $value;
	    return $this;
	}
	
	/**
	 * Returns the value for readonly
	 * @return boolean|\Sunhill\bool
	 */
	protected function get_readonly() {
	    return $this->readonly;
	}
	
// ============================== State-Handling ===========================================	
	
    /**
     * Sets the current state of this object
     * @param $state string the new state
     */
	protected function set_state(string $state) {
	    $this->state = $state;
	    return $this;
	}

    /**
     * Returns the current state of this object
     * @return string
     */
	protected function get_state() {
	    return $this->state;
	}

    /**
     * Returns true if this object is comitting right now
     * @return bool
     */
	protected function is_committing() {
	    return ($this->get_state() == 'committing');
	}
	
    /**
     * Returns true if this object is invalid
     * @return bool
     */
	protected function is_invalid() {
	    return $this->get_state() == 'invalid';
	}
	
    /**
     * Returns true if this object is loading right now
     * @return bool
     */
    protected function is_loading() {
	   return $this->get_state() == 'loading';    
	}
	
	/**
     * Raises an exception if the object is invalid
     */
    protected function check_validity() {
	    if ($this->is_invalid()) {
	        throw new PropertiesHavingException('Invalided object called.');
	    }
	}
// ==================================== Loading =========================================
	
    /**
     * Loads the object with the id $id from the storage
     * @param $id int The id of the object to load
     * @returns propertieshaving Reference to self
     * @throws PropertiesHavingException If the object is invalid
     */
	public function load($id) {
	    $this->check_validity(); // Is this object inavlid?
	    
        if ($result = $this->check_cache($id)) { // Is this object already in the cache
	        $this->set_state('invalid'); // If yes, this object is invalid
	        return $result; // Return the cache instead
	    }
	    
        $this->insert_cache($id); // Insert into cache
	    $this->set_id($id);       
	    $this->check_for_hook('LOADING','default',array($id));
	    $this->do_load();
	    $this->clean_properties();
	    $this->check_for_hook('LOADED','default',array($id));
	    return $this;
	}
	
	/**
	 * Checks if the object with the given id is already in cache
	 * @param integer $id The id to check
     * @returns bool True if it is in the cache, false if not
	 */
	protected function check_cache(int $id) {
	    return false;
	}
	
	/**
	 * Add itself to the cache
	 * @param Int $id
	 */
	protected function insert_cache(int $id) {
	}
	
    /**
     * Does the loading (has to be overwritten)
     */
	protected function do_load() {
	}
	
// ===================================== Committing =======================================
	/**
     * Stores the object into the storage
     */
    public function commit($caller=null) {
	    $this->check_validity();
    	if (!$this->is_committing()) { // Guard to protect from circular calls
	        $this->set_state('committing');
	        $this->check_for_hook('COMMITTING');
	        if ($this->get_id()) {
	            $this->update(); // This object is already in a storage
	        } else {
	            $this->insert(); // This object is new
	        }
	        $this->check_for_hook('COMMITTED');
	        $this->set_state('normal');
	    }
	    return;
	}

    /**
     * Returns if one of the properties is modified since the last commit(), rollback() or load()
     * @returns bool
     */
	protected function get_dirty() {
	    $dirty_properties = $this->get_properties_with_feature('',true);
	    return (!empty($dirty_properties));	    
	}
	
	protected function do_recommit() {
	    
	}
	
// ====================================== Updating ========================================	
	/**
     * Checks for hooks and calls do_update
     */
    protected function update() {
	    $this->check_for_hook('UPDATING');
	    $this->do_update();
	    $this->check_for_hook('UPDATED');
	}

    /**
     * Does the update work
     */
	protected function do_update() {
	    // has to be overwritten in child objects
	}
	
	/**
	 * Cleans the dirty state
	 */
	protected function clear_dirty() {
	    $this->clean_properties();
	}

// ======================================= Inserting ===========================================
	/**
     * Checks for hooks and calls do_insert
     */
    protected function insert() {
	    $this->check_for_hook('INSERTING');
	    $this->do_insert();
	    $this->check_for_hook('INSERTED');
	}

	/**
	 * Does the insert work
	 * @param bool $recommit
	 */
	protected function do_insert() {
	   // has to be overwritten in child objects 
	}
	
	// ====================================== Deleting ==========================================
	/**
     * Checks for hooks and calls do_delete and clears the cache
     */
    public function delete() {
	    $this->check_for_hook('DELETING');
	    $this->do_delete();
	    $this->check_for_hook('DELETED');
	    $this->clear_cache_entry();
	}
	
    /**
     * Does the delete work
     */
	protected function do_delete() {
	   // Has to be overwritten in child objects 
	}
	
    /**
     * Clears the cache (reomves this entry)
     */
	protected function clear_cache_entry() {
	   // Has to be overwritten in child objects 
	}
	
	// ===================================== Property-Handling ========================================	

    /**
	 * Is called by the constructor to initialize the properties
     * Child objects always have to call the parent method
     */
	protected function copy_properties() {
	    $this->properties = array();
	    foreach (static::$property_definitions as $name => $property) {
	        $this->properties[$name] = clone $property;
	        //$this->properties[$name]->set_class(get_class($this));
	        $this->properties[$name]->set_owner($this);
	    }
	}

    /**
     * Undirties all properties 
     */
	public function clean_properties() {
	    foreach ($this->properties as $property) {
	        $property->set_dirty(false);
	    }
	}
	
    /**
     * Searches for a property with the given name. If there is one, return its value. If not pass it to the parent __get method
     * @param $name string The name of the unknown member variable
     */
	public function &__get($name) {
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
	public function __set($name,$value) {
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
	protected function handle_unknown_property($name,$value) {
	   return false;    
	}
	
	/**
	 * Returns the property object with the given name or raises an exception if there is no such property
	 * @param string $name Name of the property
	 * @return oo_property
	 */
	public function get_property(string $name,bool $return_null=false) {
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
	public function get_properties_with_feature(string $feature='',$dirty=null,$group=null) {
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
	           if ($property->has_feature($feature)) {
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

	protected function dynamic_add_property($name,$type) {
	    $property = static::create_property($name, $type);
	    $property->set_owner($this);
	    $this->properties[$name] = $property;
	    return $property;	    
	}
	// ========================== Statische Methoden ================================
	
	protected static $property_definitions;
	
	public static function initialize_properties() {
 	       static::$property_definitions = array();
	       static::setup_properties();
	}
	
	protected static function setup_properties() {
	    
	}

	private static function get_calling_class() {
	    $caller = debug_backtrace();
	    return $caller[4]['class'];
	}
	
	protected static function create_property($name,$type,$class=null) {
	    $property_name = '\Sunhill\ORM\Properties\oo_property_'.$type;
	    $property = new $property_name();
	    $property->set_name($name);
	    $property->set_type($type);
	    $property->set_class(is_null($class)?Classes::get_class_name(self::get_calling_class()):$class);
	    $property->initialize();
	    return $property;
	}
	
	protected static function add_property($name,$type) {
	    $property = static::create_property($name, $type);
	    static::$property_definitions[$name] = $property;
	    return $property;
	}
	
	public static function get_property_object($name) {
	    static::initialize_properties();
	    if (isset(static::$property_definitions[$name])) {
	        return static::$property_definitions[$name];
	    } else {
	        return null;
	    }
	}
	
	/**
	 * Liefert alle Properties zurück, die ein bestimmtes Feature haben
	 * @param string $feature, wenn ungleich null, werden nur die Properties zurückgegeben, die ein bestimmtes Feature haben
	 * @param bool $dirty, wenn true, dann nur dirty-Properties, wenn false dann nur undirty, wenn null dann alle
	 * @param string $group, wenn nicht null, dann werden die Properties nach dem Ergebnis von get_$group gruppiert
	 * @return unknown[]
	 */
	public static function static_get_properties_with_feature(string $feature='',$group=null) {
	    $result = array();
	    if (isset($group)) {
	        $group = 'get_'.$group;
	    }
	    if (empty(static::$property_definitions)) {
	        static::setup_properties();
	        if (empty(static::$property_definitions)) {
	            return $result;
	        }
	    }
	    foreach (static::$property_definitions as $name => $property) {
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
	            if ($property->has_feature($feature)) {
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
	
	public static function get_property_info($name) {
	    static::initialize_properties();
	    return static::$property_definitions[$name];
	}
	
	public static function search() {
	     $query = new query_builder();
	     $query->set_calling_class(get_called_class());
	     return $query;
	}
	
	protected static function timestamp($name) {
	    $property = self::add_property($name, 'timestamp');
	    return $property;
	}
	
	protected static function integer($name) {
	    $property = self::add_property($name, 'integer');
	    return $property;
	}
	
	protected static function varchar($name) {
	    $property = self::add_property($name, 'varchar');
	    return $property;
	}
	
	protected static function object($name) {
	    $property = self::add_property($name, 'object');
	    return $property;
	}
	
	protected static function text($name) {
	    $property = self::add_property($name, 'text');
	    return $property;
	}
	
	protected static function enum($name) {
	    $property = self::add_property($name, 'enum');
	    return $property;
	}
	
	protected static function datetime($name) {
	    $property = self::add_property($name, 'datetime');
	    return $property;
	}
	
	protected static function date($name) {
	    $property = self::add_property($name, 'date');
	    return $property;
	}
	
	protected static function time($name) {
	    $property = self::add_property($name, 'time');
	    return $property;
	}
	
	protected static function float($name) {
	    $property = self::add_property($name, 'float');
	    return $property;
	}
	
	protected static function arrayofstrings($name) {
	    $property = self::add_property($name, 'array_of_strings');
	    return $property;
	}
	
	protected static function arrayofobjects($name) {
	    $property = self::add_property($name, 'array_of_objects');
	    return $property;
	}
	
    /**
     * Defines a calculated property with the name $name
     * @param $name The name of this property
     * @see Sunhill/ORM/Properties/
     */     
	protected static function calculated($name) {
	    $property = self::add_property($name, 'calculated');
	    return $property;
	}
	
}
