<?php

namespace Sunhill\Properties;

/**
 * @todo Muss das wirklich sein, oder kann man das auf das Storage auslagern
 */
use Illuminate\Support\Facades\DB;

/** 
 * Die folgenden Defines legen Konstanten fest, die get_diff_array als (optionalem) Parameter übergeben
 * werden können
 * @var unknown
 */
define ('PD_VALUE',1); // In das Diff Array werden die Werte direkt von $value und $shadow kopiert
define ('PD_ID',2);    // Bei Objektreferenzen werden statt dessen nur die IDs kopiert
define ('PD_KEEP',3);  // Ist das Objekt bereits geladen, gib dies zurück, ansonsten die ID

/**
 * Basisklasse für Exceptions, die etwas mit Properties zu tun haben
 * @author lokal
 *
 */
class PropertyException extends \Sunhill\SunhillException {}

/**
 * Die Exception für ungültige Wertzuweisungen an dieses Property
 * @author lokal
 *
 */
class InvalidValueException extends PropertyException {}

/**
 * Basisklasse für Properties
 * @author lokal
 *
 */
class oo_property extends \Sunhill\base {
	
    /**
     * Über das Feature-Array werden die Eigenschaften des Properties definiert. Features können über
     * has_feature() abgefragt werden
     * @var array
     */
    protected $features = array();
    
    /**
     * Der Owner ist das besitzende Objekt dieses Properties (also eine von propertieshaving abgeleitete Klasse)
     * Wird über get_owner() und set_owner() abgefragt bzw. geändert.
     * @var \Sunhill\propertieshaving
     */
	protected $owner;
	
    /**
     * Der Name der Property
     * Wird über get_name() und set_name() abgefragt bzw. festgelegt
     * @var string
     */
	protected $name;
	
	/**
	 * Der Wert dieser Property
	 * @var void
	 */
	protected $value;
	
	/**
	 * Der Schattenwert dieser Property (also der Wert nach dem letzten commit(). Wird für ein 
	 * Rollback benötigt, sowie für die Erzeugung des diff_arrays
	 * @var void
	 */
	protected $shadow;
	
	/**
	 * Der Typ der Property. Wird in der jeweiligen Property als Defaultwert gesetzt.
	 * @var string
	 */
	protected $type;
	
	/**
	 * Der Vorgabewert für value, wenn er nicht gesetzt wird. Wenn $default null ist und $defaults_null ebenfalls
	 * ist der vorgabewert null, wenn $defaults_null false ist, gibt es keinen Vorgabewert
	 * @var void
	 */
	protected $default;
	
	/**
	 * Legt fest ob der Standardwert für value null sein soll. Wird auf true gesetzt, wenn set_default
	 * mit null aufgerufen wird. 
	 * @var bool
	 */
	protected $defaults_null;
	
	/**
	 * Gibt die Dirtyness der Property an. Ist der Wert false, wurde der Wert der Property seit der
	 * Initialisierung oder dem letzten commit() nicht verändert. Ist er true, wurde er verändert
	 * Der Zugriff sollte langfristig auch innerhalb der abgeleiteten Klassen über get_dirty() und set_dirty()
	 * erfolgen.  
	 * @var bool
	 */
	protected $dirty;
	
	/**
	 * Gibt den Initialiserungstatus der Property an. Ist der Wert false, wurde value noch nie ein Wert
	 * zugeweisen, ist er true, wurde die Property entweder bereits zugeweisen oder geladen.
	 * @var bool
	 */
	protected $initialized=false;
	
	/**
	 * Gibt an, ob die Property nur lesen (true) oder auch beschreibbar (false) ist
	 * @var bool
	 */
	protected $read_only=false;
	
	/**
	 * Name des Validators. Defaultmäßig ein Basisvalidator, der alle Werte durchwinkt.
	 * @var string
	 */
	protected $validator_name = 'validator_base';
	
	/**
	 * Speichert das validator-Objekt
	 * @var \Sunhill\Validators\validator_base
	 */
	protected $validator;
	
	/**
	 * Speichert die Hooks für dieses Property
	 * @var array
	 */
	protected $hooks = array();
	
	protected $class;
	
	/**
	 * Gibt an, ob nach nach diesem Property suchen kann (true) oder nicht (false)
	 * @var string
	 */
	protected $searchable=false;
	
	/**
	 * Konstruktor der Property
	 * Setzt die Parameter auf default-Werte
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
	 * Initialisiert diese Property. Das ist aber nicht gleichbedeutend mit $initialized!!
	 * Hier können zusätzliche Schritte unternommen werden
	 */
	public function initialize() {
	}
	
	/**
	 * Initialisiert den validator.
	 * @throws PropertyException Wenn es den Validator nicht gibt
	 */
	protected function init_validator() {
	    $validator_name = "\\Sunhill\\Validators\\".$this->validator_name;
	    if (!class_exists($validator_name)) {
	        throw new PropertyException("Unbekannter Validator '".$this->validator_name."' aufgerufen.");
	    }
	    $this->validator = new $validator_name();    
	}

// =========================== Setter und Getter ========================================	
	public function set_owner($owner) {
	    $this->owner = $owner;
	    return $this;	    
	}

	public function get_owner() {
	    return $this->owner;
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	 
	public function get_name() {
		return $this->name;
	}
	
	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
	
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
	 * @return \Sunhill\Properties\oo_property
	 */
	final public function set_value($value,$index=null) {
		if ($this->read_only) {
			throw new PropertyException("Die Property ist read-only.");
		}
		
		// Prüfen, ob sich der Wert überhaupt ändert
		if ($this->initialized) {
            if (!is_null($index)) {
        		 if (isset($this->value[$index]) && ($this->value[$index] === $value)) {
        		     return $this;
        		 } 
            } else if ($value === $this->value) {
    		      return $this;
            }		
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
	 * Schreibt den neuen indizierten Wert nach $value
	 * @param int $index Index, der neu gesetzt werden soll
	 * @param unknown $value Wert, den dieses Element bekommen soll
	 */
	protected function do_set_indexed_value(int $index,$value) {
	    $this->value[$index] = $value;
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
	
	final public function &get_value($index=null) {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;
				$this->shadow = $this->default;
				$this->initialized = true;
			} else {
			    if (!$this->initialize_value()) {
			         throw new PropertyException("Lesender Zugriff auf nicht ininitialisierte Property: '".$this->name."'");
			    }
			}
		}
		if (is_null($index)) {
		    if ($this->is_array()) {
		        return $this;
		    } else {
		        return $this->do_get_value();
		    }
		} else {
		        return $this->do_get_indexed_value($index);
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
	
	public function get_old_value() {
		return $this->shadow;
	}
	
	/**
	 * Erzeugt ein Diff-Array. 
	 * d.h. es wird ein Array mit (mindestens) zwei Elementen zurückgebene:
	 * FROM ist der alte Wert
	 * TO ist der neue Wert
	 * @param int $type Soll bei Objekten nur die ID oder das gesamte Objekt zurückgegeben werden
	 * @return void[]|\Sunhill\Properties\oo_property[]
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
	public function get_dirty() {
		return $this->dirty;	
	}
	
	public function set_dirty($value) {
		$this->dirty = $value;
	}
	
	public function commit() {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;	
			} else {
				throw new PropertyException("Commit einer nicht initialisierten Property: '".$this->name."'");
			}
		}
		$this->dirty = false;
		$this->shadow = $this->value;
	}
	
	public function rollback() {
		$this->dirty = false;
		$this->value = $this->shadow;
	}
	
	protected function validate($value) {
		return $this->validator->validate($value);
	}
	
	public function is_array() {
		return $this->has_feature('array');
	}
	
	public function is_simple() {
		return $this->has_feature('simple');
	}
	
	public function has_feature(string $test) {
	    return in_array($test,$this->features);
	}
	
	public function deleting(\Sunhill\Storage\storage_base $storage) {
	    
	}
	
	public function deleted(\Sunhill\Storage\storage_base $storage) {
	    
	}
	
	public function delete($storage) {
	    
	}
	
	// ================================== Laden ===========================================	
	/**
	 * Wird für jede Property aufgerufen, um den Wert aus dem Storage zu lesen
	 * Ruft wiederrum die überschreibbare Methode do_load auf, die property-Individuelle Dinge erledigen kann
	 * @param \Sunhill\Storage\storage_load $loader
	 */
	final public function load(\Sunhill\Storage\storage_base $loader) {
	    $name = $this->get_name();
        $this->do_load($loader,$name);
	    $this->initialized = true; 
	    $this->dirty = false;
	}

	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name) {
	    $this->value = $loader->$name;
	}

	/**
	 * Wird aufgerufen, bevor das Property geladen wird
	 * @param \Sunhill\Storage\storage_base $storage
	 */
	public function loading(\Sunhill\Storage\storage_base $storage) {
	    // Macht nix
	}
	
	/**
	 * Wird aufgerufen, nachdem das Property geladen ist
	 * @param \Sunhill\Storage\storage_base $storage
	 */
	public function loaded(\Sunhill\Storage\storage_base $storage) {
	    // Macht nix
	}
	
	// ============================= Insert =========================================	
	/**
	 * Wird für jede Property aufgerufen, um den Wert in das Storage zu schreiben
	 */
	public function insert(\Sunhill\Storage\storage_base $storage) {
	    $this->do_insert($storage,$this->get_name());
	    $this->dirty = false;	    
	}
	
	/**
	 * Wird im Falle eines ReInserts (zweiter Lauf bei zirkulären Referenzen)
	 * aufgerufen
	 * @param \Sunhill\Storage\storage_base $storage
	 */
	public function reinsert(\Sunhill\Storage\storage_base $storage) {
	    // Macht standardmäßig nix
	}
	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Speichermethoden zu verwenden
	 * @param \Sunhill\Storage\storage_insert $storage
	 * @param string $tablename
	 * @param string $name
	 */
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
	    $storage->set_entity($name, $this->value);
	}
	
    /**
     * Wird vor dem Einfügen aufgerufen
     * @param \Sunhill\Storage\storage_base $storage
     */
	public function inserting(\Sunhill\Storage\storage_base $storage) {
        // Macht nix	    
	}
	
	/**
	 * Wird nach dem Einfügen aufgerufen
	 * @param \Sunhill\Storage\storage_base $storage
	 */
	public function inserted(\Sunhill\Storage\storage_base $storage) {
	    // Macht nix
	}

// ================================= Update ====================================	
	public function update(\Sunhill\Storage\storage_base $storage) {
	    if ($this->dirty || $this->owner->get_needs_recommit()) {
            $diff = $this->get_diff_array(PD_KEEP);
	        $this->get_owner()->check_for_hook('UPDATING_PROPERTY',$this->get_name(),$diff);
    	    $this->do_update($storage,$this->get_name());
    	    $this->get_owner()->check_for_hook('UPDATED_PROPERTY',$this->get_name(),$diff);
    	    $this->dirty = false;
	    }
	}
	
	protected function do_update(\Sunhill\Storage\storage_base $storage,string $name) {
        $diff = $this->get_diff_array(PD_ID);
	    $storage->set_entity($name,$diff);	    
	}
	
    /**
     * Wird aufgerufen, bevor das Update stattfindet
     * @param \Sunhill\Storage\storage_base $storage
     */
	public function updating(\Sunhill\Storage\storage_base $storage) {
	    // Macht nix
	}
	
    /**
     * Wird aufgerufen, nachdem das Update stattgefunden hat
     * @param \Sunhill\Storage\storage_base $storage
     */
	public function updated(\Sunhill\Storage\storage_base $storage) {
	   // Macht nix
	}
	
	public function add_hook($action,$hook,$subaction,$target) {
	   $this->hooks[] = ['action'=>$action,'hook'=>$hook,'subaction'=>$subaction,'target'=>$target];    
	}
	
	// **************************** Suchfunktionen **********************************
	final public function get_where($relation,$value,$letter) {
	    if (!$this->is_allowed_relation($relation, $value)) {
	        throw new PropertyException("Nicht erlaubte Relation '$relation'");
	    }
	    return $this->get_individual_where($relation,$value,$letter);
	}
	
	public function get_table_name($relation,$where) {
	   $classname = $this->get_class();
	   return $classname::$table_name;
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "on a.id = $letter.id";
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    if ($relation == 'in') {
	        $result = $letter.'.'.$this->get_name()." in (";
	        $first = true;
	        foreach ($value as $single_value) {
	            if (!$first) {
	                $result .= ',';
	            }
	            $first = false;
	           $result .= DB::connection()->getPdo()->quote($single_value);
	        }
	        return $result.')';
	    }
	    return $letter.'.'.$this->get_name().$relation."'".$value."'";	    
	}
	
	protected function is_allowed_relation(string $relation,$value) {
	    switch ($relation) {
	        case '=':
	        case '<':
	        case '>':
	        case '>=':
	        case '<=':
	        case '<>':
                return is_scalar($value); break;
	        case 'in':
	            return is_array($value); break;
	        default:
	            return false;
	    }
	}
	
	protected function escape(string $value) {
	    return DB::connection()->getPdo()->quote($value);	    
	}
	
}