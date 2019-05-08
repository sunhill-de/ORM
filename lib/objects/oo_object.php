<?php

namespace Sunhill\Objects;

use App;
use Illuminate\Support\Facades\DB;

class ObjectException extends \Exception {}
class UnknownPropertyException extends ObjectException {}

class oo_object extends \Sunhill\propertieshaving {

    public static $table_name = 'objects';
    
	public $default_ns = '\\App';		
	
// ================================ Laden ========================================	
	/**
	 * Prüft, ob das Objekt mit der ID $id im Cache ist, wenn ja, liefert es ihn zurück
	 * @param integer $id
	 */
	protected function check_cache(int $id) {
	    if (self::is_cached($id)) {
	        return self::load_object_of($id);
	    }
	    return false;
	}
	
	/**
	 * Trägt sich selbst im Cache ein
	 * @param Int $id
	 */
	protected function insert_cache(int $id) {
	    self::load_id_called($id,$this);	    
	}
	
	protected function do_load() {
	    if (!$this->is_loading()) {
	        $this->state = 'loading';
            $this->load_core_object();
	        $this->load_simple_fields();
            $this->load_other_properties();
	        $this->state = 'normal';
	    }
	}
	
	private function load_core_object() {
        $core = DB::table('objects')->select('updated_at','created_at')->where('id','=',$this->get_id())->first();
	    $this->updated_at = $core->updated_at;
	    $this->created_at = $core->created_at;
	}
	
	private function load_simple_fields() {
	    $properties = $this->get_properties_with_feature('simple',null,'class');
	    foreach ($properties as $class_name => $fields_of_class) {
    	        if ($class_name == '\\Sunhill\Objects\oo_object') {
    	            continue; // Wurde schon geladen
    	        } 	           
    	        $fields = DB::table($class_name::$table_name)->where('id','=',$this->get_id())->first();
    	        foreach ($fields_of_class as $field_name => $field) {
    	            $this->$field_name = $fields->$field_name;
    	        }
	    }
	}
	
	private function load_other_properties() {
	   $properties = $this->get_properties_with_feature('');
	   foreach ($properties as $name => $property) {
	      $property->load($this->get_id()); 
	   }
	}
	
// ========================= Einfügen =============================	
	protected function do_insert() {
        $this->insert_core_object();
	    $simple_fields = $this->get_properties_with_feature('simple',null,'class');
        foreach ($simple_fields as $class_name => $fields_of_class) {
            if ($class_name == '\\Sunhill\Objects\oo_object') {
                continue; // Wurde schon gespeichert
            } else {
                $values = array();
                foreach ($fields_of_class as $field_name => $field) {
                    $values[$field_name] = $field->get_value();
                }
                $values['id'] = $this->get_id();
                DB::table($class_name::$table_name)->insert($values);
            }
        }
	}
	
	private function insert_core_object() {
	    $id = DB::table('objects')->insertGetId(['classname'=>get_class($this),
	                                             'created_at'=>DB::raw('now()'),
	                                             'updated_at'=>DB::raw('now()')
	    ]);
	    $core = DB::table('objects')->where('id','=',$id)->first();
	    $this->created_at = $core->created_at;
	    $this->updated_at = $core->updated_at;
	    $this->set_id($id);
	}

// ========================== Aktualisieren ===================================	
	protected function do_update() {
	    $this->update_core_object();
	    $simple_fields = $this->get_properties_with_feature('simple',true,'class');
	    foreach ($simple_fields as $class_name => $fields_of_class) {
            $values = array();
	        foreach ($fields_of_class as $field_name => $field) {
	            $values[$field_name] = $field->get_value();
	        }
            DB::table($class_name::$table_name)->
                      updateOrInsert(['id'=>$this->get_id()],$values);
	    }
	}
	
	private function update_core_object() {
	    DB::table('objects')->where('id','=',$this->get_id())->update(['updated_at'=>DB::raw('now()')]);
	}
	
	/**
	 * Erzeugt ein leeres neues Objekt
	 */
	public function create_empty() {
		
	}
	
	// ================================= Löschen =============================================
	protected function do_delete() {
	    $this->set_state('deleting');
	    $this->delete_core_object();
	    $this->delete_simple_fields();
	    $this->set_state('invalid');
	}
	
	private function delete_core_object() {
	    DB::table('objects')->where('id','=',$this->get_id())->delete();
	}
	
	private function  delete_simple_fields() {
	    $fields = $this->get_properties_with_feature('simple',null,'class');
	    foreach ($fields as $class_name=>$fields) {
	        if (!empty($class_name)) {
	            DB::table($class_name::$table_name)->where('id','=',$this->get_id())->delete();
	        }
	    }
	}
	
	protected function clear_cache_entry() {
	    unset(self::$objectcache[$this->get_id()]); // Cache-Eintrag löschen
	}
	
	// ********************* Property Handling *************************************	
	
	protected function setup_properties() {
	    parent::setup_properties();
	    $this->timestamp('created_at');
	    $this->timestamp('updated_at');
	    $this->add_property('tags','tags');
	    $this->add_property('externalhooks','externalhooks');
	    $this->add_property('attribute_loader','attribute_loader');
	}
	
	protected function timestamp($name) {
		$property = $this->add_property($name, 'timestamp');
		return $property;
	}
	
	protected function integer($name) {
		$property = $this->add_property($name, 'integer');
		return $property;
	}
	
	protected function varchar($name) {
		$property = $this->add_property($name, 'varchar');
		return $property;		
	}
	
	protected function object($name) {
		$property = $this->add_property($name, 'object');
		return $property;
	}
	
	protected function text($name) {
		$property = $this->add_property($name, 'text');
		return $property;
	}
	
	protected function enum($name) {
		$property = $this->add_property($name, 'enum');
		return $property;		
	}
	
	protected function datetime($name) {
		$property = $this->add_property($name, 'datetime');
		return $property;
	}
	
	protected function date($name) {
		$property = $this->add_property($name, 'date');
		return $property;
	}
	
	protected function time($name) {
		$property = $this->add_property($name, 'time');
		return $property;
	}
	
	protected function float($name) {
		$property = $this->add_property($name, 'float');
		return $property;
	}
	
	protected function arrayofstrings($name) {
		$property = $this->add_property($name, 'array_of_strings');
		return $property;
	}
	
	protected function arrayofobjects($name) {
		$property = $this->add_property($name, 'array_of_objects');
		return $property;
	}
	
	/**
	 * Hebt das momentane Objekt auf eine abgeleitete Klasse an
	 * @param String $newclass
	 * @throws ObjectException
	 * @return oo_object
	 */
	public function promote(String $newclass) {
	    if (!class_exists($newclass)) {
	        throw new ObjectException("Die Klasse '$newclass' existiert nicht.");    
	    }
	    if (!is_subclass_of($newclass, get_class($this))) {
	        throw new ObjectException("'$newclass' ist keine Unterklassen von '".get_class($this)."'");
	    }
	    $this->pre_promotion($newclass);
	    $newobject = $this->promotion($newclass);
	    $newobject->post_promotion($this);
	    return $newobject;
	}
	
	/**
	 * Wird aufgerufen, bevor die Promovierung stattfindet
	 * @param String $newclass
	 */
	protected function pre_promotion(String $newclass) {
	   return true; // Mach in der urspünglichen Variante nix
	}
	
	/**
	 * Die eigentliche Promovierung
	 * @param String $newclass
	 */
	private function promotion(String $newclass) {
	    $newobject = new $newclass; // Neues Objekt erzeugen
	    $this->copy_to($newobject); // Die Werte bis zu dieser Hirarchie können kopiert werden
	    DB::table('objects')->where('id','=',$this->get_id())->update(['classname'=>$newclass]);
	    return $newobject;
	}
	
	protected function copy_to(oo_object $newobject) {
	    $newobject->set_id($this->get_id());
	    foreach ($this->properties as $property) {
	        $name = $property->get_name();
	        switch ($property->get_type()) {
	            case 'array_of_objects':
	            case 'array_of_strings':
	            case 'external_references':
	            case 'tags':
	                for ($i=0;$i<count($this->$name);$i++) {
	                    $newobject->$name[] = $this->$name[$i];
	                }
	                break;
	            default:
	                $newobject->$name = $this->$name;
	        }
	    }
	}
	
	/**
	 * Wird aufgerufen, nachdem das Objekt promoviert wurde
	 * @param oo_object $newobject
	 */
	public function post_promotion(oo_object $from) {
	    
	}
	
	public function degrade(String $newclass) {
	    if (!class_exists($newclass)) {
	        throw new ObjectException("Die Klasse '$newclass' existiert nicht.");
	    }
	    if (!is_subclass_of(get_class($this), $newclass)) {
	        throw new ObjectException("'".get_class($this)."' ist keine Unterklasse von '$newclass'");
	    }
	    $this->pre_degration($newclass);
	    $newobject = $this->degration($newclass);
	    $newobject->post_degration($this);
	    return $newobject;
	}
	
	protected function pre_degration(String $newclass) {
	       return true;    
	}
	
	protected function degration(String $newclass) {
	    $newobject = new $newclass; // Neues Objekt erzeugen
	    $newobject->copy_from($this); // Die Werte bis zu dieser Hirarchie können kopiert werden
	    DB::table('objects')->where('id','=',$this->get_id())->update(['classname'=>$newclass]);
	    return $newobject;
	    
	}
	
	public function copy_from(oo_object $source) {
	    $this->set_id($source->get_id());
	    foreach ($this->properties as $property) {
	        $name = $property->get_name();
	        switch ($property->get_type()) {
	            case 'array_of_objects':
	            case 'array_of_strings':
	            case 'external_references':
	            case 'tags':
	                for ($i=0;$i<count($source->$name);$i++) {
	                    $this->$name[] = $source->$name[$i];
	                }
	                break;
	            default:
	                $this->$name = $source->$name;
	        }
	    }
	}
	
	public function post_degration(oo_object $from) {
	    
	}
	
	public function get_inheritance() {
	     $parent_class_names = array();
	     $parent_class_name = get_class($this);
	     do {
	         if ($parent_class_name == 'Sunhill\\Objects\\oo_object') {
	             array_shift($parent_class_names);
	             return $parent_class_names;
	         }
	         $parent_class_names[] = $parent_class_name;
	         //array_unshift($parent_class_names,$parent_class_name);
	     } while ($parent_class_name = get_parent_class($parent_class_name));
	     return $parent_class_names;
	}
	
	/**
	 * Wird für komplexe Aufgabe aufgerufen
	 * @param string $action
	 * @param string $hook
	 * @param string $subaction
	 * @param unknown $destination
	 */
	protected function set_complex_hook(string $action,string $hook,string $subaction,$destination) {
	    $this->hooks[$action][$subaction][] = array('destination'=>$destination,'hook'=>$hook);
	    
	    $parts = explode('.',$subaction);
	    $field = array_shift($parts);
	    $restaction = implode('.',$parts);
	    $property = $this->get_property($field);
	    $property->add_hook($action,$hook,$restaction,$destination);
	//    $this->add_hook('EXTERNAL','complex_changed',$field,$this,array('action'=>$action,'hook'=>$hook,'field'=>$restaction));
	}
	
	protected function complex_changed($params) {
	    $target = $params['subaction'];
        if (isset($target)) {
            $fieldname = $params['payload']['field'];
            $hookname  = $params['payload']['hook'];
            $this->$target->add_hook($params['payload']['action'],$hookname,$fieldname,$this);
        }
	}
	
	protected function target_equal($test1,$test2) {
	    if ($test1 instanceof oo_object) {
	        $test1 = $test1->get_id();
	    }
	    if ($test2 instanceof oo_object) {
	        $test2 = $test2->get_id();
	    }
	    return ($test1 === $test2);
	}
	
	public function array_field_new_entry($name,$index,$value) {
	    $this->check_for_hook('PROPERTY_ARRAY_NEW',$name,[$value]); 
	}
	
	public function array_field_removed_entry($name,$index,$value) {
	    $this->check_for_hook('PROPERTY_ARRAY_REMOVED',$name,[$value]);	    
	}
	
	protected function handle_unknown_property($name,$value) {
	    if ($attribute = \Sunhill\Properties\oo_property_attribute::search($name)) {
	        return $this->add_attribute($attribute,$value);
	    } else {
	        return parent::handle_unknown_property($name, $value);
	    }
	}
	
	private function add_attribute($attribute,$value) {
	   $this->check_allowed_class($attribute);
	   // Es gibt das Attribut und es darf für dieses Objekt benutzt werden
	   $this->check_for_hook('ATTRIBUTE_ADDING',$attribute->name,[$value]);
	   if (!empty($attribute->property)) {
	       $property_name = $attribute->property;
	   } else {
	       $property_name = 'attribute_'.$attribute->type;
	   }
	   $property = $this->add_property($attribute->name, $property_name);
	   $property->set_value($value);
	   $property->set_dirty(true);
	   return true;
	}
	
	private function check_allowed_class($attribute) {
	    $allowed_classes = explode(',',$attribute->allowedobjects);
	    if (!empty($allowed_classes)) {
	        $allowed = false;
	        foreach ($allowed_classes as $class) {
	            if (is_a($this,$class)) {
	                $allowed = true;
	            }
	        }
	    }
	    if (!$allowed) {
	        throw new \Sunhill\Properties\AttributeException("Das Attribut '".$attribute->name."' ist nicht für dieses Objekt erlaubt.");
	    }	    
	}
// ****************** Migration **********************************
	/**
	 * Wird aufgerufen, um zu prüfen, ob die Datenbank auf dem gleichen Stand wie dieses
	 * Objekt ist.
	 * @todo Dies ist ein Kandidat für eine statische Methode
	 */
	public function migrate() {
	    if ($this->table_exists()) {
    	    $current = $this->get_current_properties();
    	    $database = $this->get_database_properties();
    	    $this->remove_columns($current,$database);
    	    $this->add_columns($current,$database);
    	    $this->alter_colums($current,$database);
	    } else {
	        $this->create_table();
	    }
	}
	
	/**
	 * Prüft, ob die Zieltabelle überhaupt existiert. 
	 * @return boolean
	 */
	private function table_exists() {
	    $tables = DB::select(DB::raw("SHOW TABLES LIKE '".$this::$table_name."'"));
	    foreach ($tables as $name => $table) {
	        foreach ($table as $field) {
    	        if ($field == $this::$table_name) {
    	            return true;
    	        } 
	        }
	    }	    
	    return false;
	}
	
	/**
	 * Passt die unterschiedliche Benennung von Datentypen im Objekt und in der Datenbank an
	 * @param string $type
	 * @return string
	 */
	private function map_type($info) {
	    switch ($info['type']) {
	        case 'integer':
	            return 'int(11)'; break;
	        case 'varchar':
	            return 'varchar('.$info['maxlen'].')'; break;
	        case 'enum':
	            return 'enum('.$info['enum'].')'; break;
	        default:
	            return $info['type'];
	    }
	}
	
	/**
	 * Erzeugt eine neue Tabelle aus den vorhandenen Daten
	 */
	private function create_table() {
	    $statement = 'create table '.$this::$table_name.' (id int primary key';
	    $simple = $this->get_current_properties();
	    foreach ($simple as $field => $info) {
	        $statement .= ','.$field.' '.$this->map_type($info);
	    }
	    $statement .= ')';
	    DB::statement($statement);
	}
	
	private function get_current_properties() {
	   $properties = $this->get_properties_with_feature('simple',null,'class');
	   $result = array();
	   foreach ($properties[get_class($this)] as $property) {
	       $result[$property->get_name()] = ['type'=>$property->get_type()];
	       switch ($property->get_type()) {
	           case 'varchar':
	               $result[$property->get_name()]['maxlen'] = $property->get_maxlen();
	               break;
	           case 'enum':
	               $first = true;
	               $resultstr = '';
	               foreach ($property->get_enum_values() as $value) {
	                   if (!$first) {
	                       $resultstr .= ',';	                       
	                   }
	                   $resultstr .= "'$value'";
	                   $first = false;
	               }
	               $result[$property->get_name()]['enum'] = $resultstr;
	               break;
	       }
	   }
	   return $result;
	}
	
	private function get_database_properties() {
	     $fields = DB::select(DB::raw("SHOW COLUMNS FROM ".$this::$table_name));
         $result = array();
         foreach ($fields as $field) {
            $result[$field->Field] = ['type'=>$field->Type,'null'=>$field->Null];        
         }
	     return $result;
	}
	
	private function remove_columns($current,$database) {
	    foreach ($database as $name => $info) {
	        if (!array_key_exists($name,$current) && ($name !== 'id')) {
	            DB::statement("alter table ".$this::$table_name." drop column ".$name);
	        }
	    }
	}
	
	private function add_columns($current,$database) {
	    foreach ($current as $name => $info) {
	        if (!array_key_exists($name,$database)) {
                $statement = 'alter table '.$this::$table_name." add column ".$name." ";
                $statement .= $this->map_type($info);
	            DB::statement($statement);
	        }	        
	    }
	}
	
	private function alter_colums($current,$database) {
	    
	    foreach ($current as $name => $info) {
	        if (array_key_exists($name,$database)) {
	            $type = $this->map_type($info);
	            if ($type !== $database[$name]['type']) {
                    $statement = 'alter table '.$this::$table_name.' change column '.$name.' '.$name.' '.$type;
                    DB::statement($statement);
	            }
	        }
	    }
	}
	
	// ***************** Statische Methoden ***************************	
	
	private static $objectcache = array();
	
	/**
	 * Ermittelt den Klassennamen von dem Object mit der ID $id
	 * @param int $id ID des Objektes von dem der Klassennamen ermittelt werden soll
	 * @return string Der Klassenname
	 */
	public static function get_class_name_of($id) {
	    $object = DB::table('objects')->where('id','=',$id)->first(); 
	    if (empty($object)) {
	        return false;
	    }
	    return $object->classname;
	}
	
	/**
	 * Diese Methode wird von $this->load() aufgerufen, wenn ein Objekt über den lader geladen wurde. Sie soll das Objekt in den Cache eintragen
	 * @param int $id
	 */
	public static function load_id_called(int $id,oo_object $object) {
	    self::$objectcache[$id] = $object;
	}
	
	/**
	 * Erzeugt ein passendes Objekt zur übergebenen ID
	 * @param int $id ID des Objektes von dem ein Objekt erzeugt werden soll
	 * @return oo_object oder Abkömmling
	 */
	public static function load_object_of($id) {
	    if (isset(self::$objectcache[$id])) {
	        return self::$objectcache[$id];
	    } else {
	        if (($classname = self::get_class_name_of($id)) === false) {
	            return false;
	        }
	        $object = new $classname();
	        $object = $object->load($id);
	        return $object;
	    }
	}
	
	public static function flush_cache() {
	    self::$objectcache = array();
	}
	
	/**
	 * Liefert zurück, ob sich ein Objekt mit der ID $id im Cache befindet
	 * @param int $id
	 * @return bool, true, wenn im Cache sonst false
	 */
	public static function is_cached(int $id) {
	    return isset(self::$objectcache[$id]);
	}
}