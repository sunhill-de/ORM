<?php

namespace Sunhill\Properties;

//use DeepCopy\Exception\PropertyException;
use Illuminate\Support\Facades\DB;
class PropertyException extends \Exception {}

class InvalidValueException extends PropertyException {}

class oo_property extends \Sunhill\base {
	
    protected $features = array();
    
	protected $owner;
	
	protected $name;
	
	protected $value;
	
	protected $shadow;
	
	protected $type;
	
	protected $default;
	
	protected $defaults_null;
	
	protected $dirty;
	
	protected $initialized=false;
	
	protected $read_only;
	
	protected $validator_name = 'validator_base';
	
	protected $validator;
	
	protected $hooks = array();
	
	protected $class;
	
	protected $searchable=false;
	
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
	
	public function initialize() {
		$this->initialized = true;
	}
	
	protected function init_validator() {
	    $validator_name = "\\Sunhill\\Validators\\".$this->validator_name;
	    $this->validator = new $validator_name();    
	}
	
	public function set_owner($owner) {
	    $this->owner = $owner;
	    return $this;	    
	}

	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	 
	public function get_name() {
		return $this->name;
	}
	/**
	 * @todo Methode kann entfernt werden
	 */
	public function load_value($value) {
		$this->value = $value;
		$this->initialized = true;
		$this->dirty = false;
		return $this;		
	}
	
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

	protected function do_set_value($value) {
	    $this->value = $value;
	}
	
	protected function do_set_indexed_value($index,$value) {
	    $this->value[$index] = $value;
	}
	
	protected function value_changed($from,$to) {
	    
	}
	
	final public function &get_value($index=null) {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;
				$this->shadow = $this->default;
				$this->initialized = true;
			} else {
			    throw new PropertyException("Lesender Zugriff auf nicht ininitialisierte Property: '".$this->name."'");
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
	
	protected function &do_get_value() {
	    return $this->value;    
	}
	
	protected function &do_get_indexed_value($index) {
	    return $this->value[$index];
	}
	
	public function get_old_value() {
		return $this->shadow;
	}
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function get_dirty() {
		return $this->dirty;	
	}
	
	public function set_dirty($value) {
		$this->dirty = $value;
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
	
	public function is_array() {
		return $this->has_feature('array');
	}
	
	public function is_simple() {
		return $this->has_feature('simple');
	}
	
	public function has_feature(string $test) {
	    return in_array($test,$this->features);
	}
	
	/**
	 * Wird aufgerufen, bevor das Elternobjekt ein update erhält
	 */
	public function updating(int $id) {
	    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt ein update erhalten hat
	 */
	public function updated(int $id) {
	    $this->commit();
	}
	
	/**
	 * Wird aufgerufen, bevor das Elternobjekt eingefügt wurde
	 */
	public function inserting() {
	    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 */
	public function inserted(int $id) {
	    $this->commit();	    
	}
	
	public function deleting(int $id) {
	    
	}
	
	public function deleted(int $id) {
	    
	}
	
	public function get_diff_array() {
	    return array('FROM'=>$this->get_old_value(),
	                 'TO'=>$this->get_value());
	}
	
	/**
	 * Wird für jede Property aufgerufen, um den Wert aus dem Storage zu lesen
	 * Ruft wiederrum die überschreibbare Methode do_load auf, die property-Individuelle Dinge erledigen kann
	 * @param \Sunhill\Storage\storage_load $loader
	 */
	final public function load(\Sunhill\Storage\storage_base $loader) {
	    $name = $this->get_name();
        $this->do_load($loader,$name);
	    $this->value = $loader->$name;
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
	 * Wird für jede Property aufgerufen, um den Wert in das Storage zu schreiben
	 */
	public function insert(\Sunhill\Storage\storage_base $storage) {
	    $this->do_insert($storage,$this->get_name());
	    $this->dirty = false;	    
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
	
	public function update(\Sunhill\Storage\storage_base $storage) {
	    if ($this->dirty) {
    	    $this->do_update($storage,$this->get_name());
            $this->dirty = false;
	    }
	}
	
	protected function do_update(\Sunhill\Storage\storage_base $storage,string $name) {
        $storage->set_entity($name,$this->value);	    
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