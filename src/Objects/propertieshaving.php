<?php
/**
 * @file propertieshaving.php
 * Definiert die Klassen PropertiesHavingException und propertieshaving
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Search\query_builder;
use Sunhill\ORM\SunhillException;
use Sunhill\ORM\hookable;
use Sunhill\ORM\Facades\Classes;

/**
 * Abgeleitete Exception die im Zusammenhang mit Properties stehen
 * @author lokal
 */
class PropertiesHavingException extends SunhillException {}

/**
 * Basisklasse für Klassen, die Properties besitzen
 * Die Klasse erbt von hookable folgende Hooks:
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
	 * Liefert die Aktuelle ID des Objektes zurück (oder null, wenn das Objekt noch nicht in der Datenbank ist)
	 * @return Integer oder null
	 */
	public function get_id() {
	    return $this->id;
	}
	
	/**
	 * Legt die ID für das aktuelle Objekt fest
	 * @param Integer $id
	 */
	public function set_id($id) {
	    $this->id = $id;
	}
	/**
	 * Setzt einen neuen Wert für Readonly
	 * @param bool $value
	 * @return \Sunhill\propertieshaving
	 */
	protected function set_readonly(bool $value) {
	    $this->readonly = $value;
	    return $this;
	}
	
	/**
	 * Liefert den Wert für Readonly zurück
	 * @return boolean|\Sunhill\bool
	 */
	protected function get_readonly() {
	    return $this->readonly;
	}
	
// ============================== State-Handling ===========================================	
	
	protected function set_state(string $state) {
	    $this->state = $state;
	    return $this;
	}

	protected function get_state() {
	    return $this->state;
	}
	
	protected function is_committing() {
	    return ($this->get_state() == 'committing');
	}
	
	protected function is_invalid() {
	    return $this->get_state() == 'invalid';
	}
	
	protected function is_loading() {
	   return $this->get_state() == 'loading';    
	}
	
	protected function check_validity() {
	    if ($this->is_invalid()) {
	        throw new PropertiesHavingException('Invalides Objekt aufgerufen.');
	    }
	}
// ==================================== Loading =========================================
	
	public function load($id) {
	    $this->check_validity();
	    if ($result = $this->check_cache($id)) {
	        $this->set_state('invalid');
	        return $result;
	    }
	    $this->insert_cache($id);
	    $this->set_id($id);
	    $this->check_for_hook('LOADING','default',array($id));
	    $this->do_load();
	    $this->clean_properties();
	    $this->check_for_hook('LOADED','default',array($id));
	    return $this;
	}
	
	/**
	 * Prüft, ob das Objekt mit der ID $id im Cache ist, wenn ja, liefert es ihn zurück
	 * @param integer $id
	 */
	protected function check_cache(int $id) {
	    return false;
	}
	
	/**
	 * Trägt sich selbst im Cache ein
	 * @param Int $id
	 */
	protected function insert_cache(int $id) {
	}
	
	protected function do_load() {
	}
	
// ===================================== Committing =======================================
	public function commit($caller=null) {
	    $this->check_validity();
    	if (!$this->is_committing()) { // Guard, um zirkuläres Aufrufen vom commit zu verhindern
	        $this->set_state('committing');
	        $this->check_for_hook('COMMITTING');
	        if ($this->get_id()) {
	            $this->update(); // Der Eintrag befindet sich bereits in einem Storage
	        } else {
	            $this->insert(); // Der Eintrag ist neu
	        }
	        $this->check_for_hook('COMMITTED');
	        $this->set_state('normal');
	    }
	    return;
	}

	protected function get_dirty() {
	    $dirty_properties = $this->get_properties_with_feature('',true);
	    return (!empty($dirty_properties));	    
	}
	
	protected function do_recommit() {
	    
	}
	
// ====================================== Updating ========================================	
	protected function update() {
	    $this->check_for_hook('UPDATING');
	    $this->do_update();
	    $this->check_for_hook('UPDATED');
	}

	protected function do_update() {
	    // Muss von der abgeleiteten Klasse überschrieben werden
	}
	
	/**
	 * Wird aufgerufen, wenn der commit ausgeführt wurde (egal ob create oder update)
	 */
	protected function clear_dirty() {
	    $this->clean_properties();
	}

// ======================================= Inserting ===========================================
	protected function insert() {
	    $this->check_for_hook('INSERTING');
	    $this->do_insert();
	    $this->check_for_hook('INSERTED');
	}

	/**
	 * Führt den eigentlichen Commit aus
	 * @param bool $recommit
	 */
	protected function do_insert() {
	    
	}
	
	// ====================================== Deleting ==========================================
	public function delete() {
	    $this->check_for_hook('DELETING');
	    $this->do_delete();
	    $this->check_for_hook('DELETED');
	    $this->clear_cache_entry();
	}
	
	protected function do_delete() {
	    
	}
	
	protected function clear_cache_entry() {
	    
	}
	
	// ===================================== Property-Handling ========================================	
	/**
	 * Wird vom Constructor aufgerufen, um die Properties zu initialisieren.
	 * Abgeleitete Objekte müssen immer die Elternmethoden mit aufrufen.
	 */
	protected function copy_properties() {
	    $this->properties = array();
	    foreach (static::$property_definitions as $name => $property) {
	        $this->properties[$name] = clone $property;
	        //$this->properties[$name]->set_class(get_class($this));
	        $this->properties[$name]->set_owner($this);
	    }
	}

	public function clean_properties() {
	    foreach ($this->properties as $property) {
	        $property->set_dirty(false);
	    }
	}
	
	public function &__get($name) {
	    if (isset($this->properties[$name])) {
	        $this->check_for_hook('GET',$name,null);
	        return $this->properties[$name]->get_value();
	    } else {
	        return parent::__get($name);
	    }
	}
	
	public function __set($name,$value) {
	    if (isset($this->properties[$name])) {
	        if ($this->get_readonly()) {
	            throw new PropertiesHavingException("Property '$name' in der Readonly Phase verändert.");
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
	        throw new PropertiesHavingException("Unbekannte Property '$name'");
	    }
	}
	
	/**
	 * Behandelt unbekannte Properties. Wenn auch diese nicht behandelt werden können, wird false zurückgegeben
	 * @param unknown $name
	 * @param unknown $value
	 * @return boolean
	 */
	protected function handle_unknown_property($name,$value) {
	   return false;    
	}
	
	/**
	 * Liefert das Property-Objekt der Property $name zurück
	 * @param string $name Name der Property
	 * @return oo_property
	 */
	public function get_property(string $name,bool $return_null=false) {
	    if (!isset($this->properties[$name])) {
	        if ($return_null) {
	            return null;
	        }
	        throw new PropertiesHavingException("Unbekannter Property '$name'");
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
	
	protected static function calculated($name) {
	    $property = self::add_property($name, 'calculated');
	    return $property;
	}
	
}