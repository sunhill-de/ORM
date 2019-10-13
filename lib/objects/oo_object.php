<?php

namespace Sunhill\Objects;

use App;
use Illuminate\Support\Facades\DB;
use Sunhill\base;

class ObjectException extends \Exception {}
class UnknownPropertyException extends ObjectException {}

class oo_object extends \Sunhill\propertieshaving {

    public static $table_name = 'objects';
    
    protected static $has_keyfield = false;
    
    private $storage=null;
    
	public $default_ns = '\\App';		
	
	public function __construct() {
	    parent::__construct();
	    $this->properties['tags'] = self::create_property('tags','tags')->set_owner($this);
	    $this->properties['externalhooks'] = self::create_property('externalhooks','externalhooks')->set_owner($this);
	}
	
	final public function calculate_keyfield() {
	    if (static::$has_keyfield) {
	        return $this->unify($this->get_keyfield());
	    } else {
	        return null;
	    }
	}
	
	private function unify(string $keyfield) {
	    $id = oo_object::search()->where('keyfield','=',$keyfield)->first();
        $seed = 1;
	    while ($id) {
	        if ($id == $this->get_id()) {
	            return $keyfield;
	        }
	        $keyfield .= $seed++;
	        $id = oo_object::search()->where('keyfield','=',$keyfield)->first();
	    }
	    return $keyfield;
	}
	
	protected function get_keyfield() {
	   return;   
	}

	/**
	 * Liefert das aktuelle Storage zurück oder erzeugt eines, wenn es ein solches noch nicht gibt.
	 * @return \Sunhill\Storage\storage_base
	 */
	final protected function get_storage() {
	    return $this->create_storage();
	}
	
	/**
	 * Erzeugt ein Storage. Defaultmäßig ist es das mysql-Storage. Diese methode kann für Debug-Zwecke überschrieben werden
	 * @return \Sunhill\Storage\storage_mysql
	 */
	protected function create_storage() {
	    return new \Sunhill\Storage\storage_mysql($this);
	}
	    
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
	
	/**
	 * Läd das Objekt aus dem Storage.
	 * {@inheritDoc}
	 * @see \Sunhill\propertieshaving::do_load()
	 */
	protected function do_load() {
	    if (!$this->is_loading()) {
	        $this->state = 'loading';
	        $loader = $this->get_storage();
	        $this->walk_properties('loading',$loader);
	        $loader->load_object($this->get_id());
            $this->walk_properties('load',$loader);
            $this->load_attributes($loader);
            $this->load_external_hooks($loader);
            $this->walk_properties('loaded', $loader);
            $this->state = 'normal';
	    }
	}
	
	/**
	 * Da die Anzahl der Attribute vorher noch nicht feststeht, müssen diese nach Bedarf aus dem Storage gelesen werden
	 */
	protected function load_attributes(\Sunhill\Storage\storage_base $storage) {
	    if (empty($storage->get_entity('attributes'))) {
	        return;
	    }
	    foreach ($storage->get_entity('attributes') as $name => $value) {
	        if (!empty($value['property'])) {
	            $property_name = $value['property'];
	        } else {
	            $property_name = 'attribute_'.$value['type'];
	        }
	        $property = $this->dynamic_add_property($name, $property_name);
	        $property->load($storage);	        
	    }
	}
	
	/**
	 * Da die Anzahl der externen Hooks vorher noch nicht feststeht, müssen diese nach Bedarf aus dem Storage gelesen werden
	 */
	protected function load_external_hooks(\Sunhill\Storage\storage_base $storage) {
	    
	}
	
// ========================= Einfügen =============================	
	/**
	 * Fügt ein Objekt in das Storage ein. 
	 * Zunächst wird für jede Property inserting aufgerufen, anschließend insert und nach
	 * abschluss aller Arbeiten noch inserted.
	 */
	protected function do_insert() {
	        $storage = $this->get_storage();
	        $this->walk_properties('inserting', $storage);
            $this->walk_properties('insert',$storage);
            $this->set_id($storage->insert_object());
            $this->walk_properties('inserted',$storage);
            $this->insert_cache($this->get_id());
	}
	
// ========================== Aktualisieren ===================================	
	protected function do_update() {
	    $storage = $this->get_storage();
	    $storage->set_entity('id',$this->get_id());
	    $this->walk_properties('updating', $storage);
	    $this->walk_properties('update',$storage);
	    $storage->update_object($this->get_id());
	    $this->walk_properties('updated',$storage);
	}
		
	/**
	 * Erzeugt ein leeres neues Objekt
	 */
	public function create_empty() {
		
	}
	
	// ================================= Löschen =============================================
	protected function do_delete() {
	    $storage = $this->get_storage();
	    $this->walk_properties('deleting',$storage);
	    $this->walk_properties('delete',$storage);
	    $storage->delete_object($this->get_id());
	    $this->walk_properties('deleted',$storage);
	    $this->clear_cache_entry();
	}
	
	protected function clear_cache_entry() {
	    unset(self::$objectcache[$this->get_id()]); // Cache-Eintrag löschen
	}
	
	// ********************* Property Handling *************************************	
	
	/**
	 * Ruft für jede Property die durch $action definierte Methode auf und übergibt dieser das Storage
	 * @param string $action
	 * @param \Sunhill\Storage\storage_base $storage
	 */
	protected function walk_properties(string $action,\Sunhill\Storage\storage_base $storage) {
	    $properties = $this->get_properties_with_feature();
	    foreach ($properties as $property) {
	        $property->$action($storage);
	    }
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
	            case 'calculated':
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
	            case 'calculated':
	                break;
	            default:
	                $this->$name = $source->$name;
	        }
	    }
	}
	
	public function post_degration(oo_object $from) {
	    
	}
	
	public function get_inheritance($full=false) {
	     $parent_class_names = array();
	     $parent_class_name = get_class($this);
	     if ($full) {
	         //$parent_class_names[] = $parent_class_name;
	     }
	     do {
	         $parent_class_names[] = $parent_class_name;
	         if (($parent_class_name == 'Sunhill\\Objects\\oo_object')) {
	             if (!$full) {
	                array_shift($parent_class_names);
	             }
	             return $parent_class_names;
	         }
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
	   $property = $this->dynamic_add_property($attribute->name, $property_name);
	   $property->set_allowed_objects($attribute->allowedobjects)
	   ->set_attribute_name($attribute->name)
	   ->set_attribute_type($attribute->type)
	   ->set_attribute_property($attribute->property)
	   ->set_attribute_id($attribute->id);
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
	
// ======================= Statisches Proprtyhandling =============================	
	protected static function setup_properties() {
	    parent::setup_properties(); 
	    self::add_property('tags','tags')->searchable();
	    self::timestamp('created_at');
	    self::timestamp('updated_at');
	    self::calculated('keyfield')->searchable();
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

	// ****************** Migration **********************************
	/**
	 * Wird aufgerufen, um zu prüfen, ob die Datenbank auf dem gleichen Stand wie dieses
	 * Objekt ist.
	 */
	public static function migrate() {
	    static::initialize_properties();
	    if (self::table_exists()) {
	        $current = self::get_current_properties();
	        $database = self::get_database_properties();
	        self::remove_columns($current,$database);
	        self::add_columns($current,$database);
	        self::alter_colums($current,$database);
	    } else {
	        self::create_table();
	    }
	}
	
	/**
	 * Prüft, ob die Zieltabelle überhaupt existiert.
	 * @return boolean
	 */
	private static function table_exists() {
	    $tables = DB::select(DB::raw("SHOW TABLES LIKE '".static::$table_name."'"));
	    foreach ($tables as $name => $table) {
	        foreach ($table as $field) {
	            if ($field == static::$table_name) {
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
	private static function map_type($info) {
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
	private static function create_table() {
	    $statement = 'create table '.static::$table_name.' (id int primary key';
	    $simple = self::get_current_properties();
	    foreach ($simple as $field => $info) {
	        $statement .= ','.$field.' '.self::map_type($info);
	    }
	    $statement .= ')';
	    DB::statement($statement);
	}
	
	private static function get_current_properties() {
	    $properties = self::static_get_properties_with_feature('simple','class');
	    $result = array();
	    if (!isset($properties[get_called_class()])) {
	        return $result;
	    }
	    foreach ($properties[get_called_class()] as $property) {
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
	
	private static function get_database_properties() {
	    $fields = DB::select(DB::raw("SHOW COLUMNS FROM ".static::$table_name));
	    $result = array();
	    foreach ($fields as $field) {
	        $result[$field->Field] = ['type'=>$field->Type,'null'=>$field->Null];
	    }
	    return $result;
	}
	
	private static function remove_columns($current,$database) {
	    foreach ($database as $name => $info) {
	        if (!array_key_exists($name,$current) && ($name !== 'id')) {
	            DB::statement("alter table ".static::$table_name." drop column ".$name);
	        }
	    }
	}
	
	private static function add_columns($current,$database) {
	    foreach ($current as $name => $info) {
	        if (!array_key_exists($name,$database)) {
	            $statement = 'alter table '.static::$table_name." add column ".$name." ";
	            $statement .= self::map_type($info);
	            DB::statement($statement);
	        }
	    }
	}
	
	private static function alter_colums($current,$database) {
	    
	    foreach ($current as $name => $info) {
	        if (array_key_exists($name,$database)) {
	            $type = self::map_type($info);
	            if ($type !== $database[$name]['type']) {
	                $statement = 'alter table '.static::$table_name.' change column '.$name.' '.$name.' '.$type;
	                DB::statement($statement);
	            }
	        }
	    }
	}
	
	
}