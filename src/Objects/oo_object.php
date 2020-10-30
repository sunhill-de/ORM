<?php
/**
 * @file oo_object.php
 * Provides the core object of the orm system named oo_object
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, SunhillException, base
 */
namespace Sunhill\ORM\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\base;
use Sunhill\ORM\SunhillException;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;

/**
 * Baseclass for errors that raise inside of oo_object
 * @author lokal
 */
class ObjectException extends SunhillException {}

/**
 * This exception indicates that an unknown property was requested
 */
class UnknownPropertyException extends ObjectException {}

/**
 * As the central class of the ORM system oo_object provides the basic function for
 * - loading and storing
 * - creating and erasing
 * - searching
 * Glossary:
 * - tags: A tag is an additional single word information that helps in grouping orm objects
 * - property: A property is a single entity of information of a single orm object (like an integer, string or date)
 * 
 * Policy:
 * - No direct database interaction. Should be handled by the storages
 * @author lokal
 */
class oo_object extends propertieshaving {

    /**
     * Static variable that stores the name of the database table.
     * @todo This should be moved to the storages in a later step
     * @var string
     */
    public static $table_name = 'objects';
    
    public static $object_infos = [
        'name'=>'object',       // A repetition of static:$object_name @todo see above
        'table'=>'objects',     // A repitition of static:$table_name
        'name_s'=>'object',     // A human readable name in singular
        'name_p'=>'objects',    // A human readable name in plural
        'description'=>'Baseclass of all other classes in the ORM system. An oo_object should\'t be initiated directly',
        'options'=>0,           // Reserved for later purposes
    ];
    
    /**
     * Internal storage for queries that have to be executed later when this object has an id
     * @var array
     */
    protected $needid_queries = [];
    
    /**
     * Constructor for all orm classes. As a child of properties_having it calls its derrived constructor wich in turn initializes the properties.
     * Additionally it defines a few own intenal properties (tags and externalhooks)
     */
	public function __construct() {
	    parent::__construct();
	    $this->properties['tags'] = self::create_property('tags','tags','object')->set_owner($this);
	    $this->properties['externalhooks'] = self::create_property('externalhooks','externalhooks','object')->set_owner($this);
	}
	
	// ========================================== NeedID-Queries ========================================
	
	
	/**
	 * Adds another entry to the needid_queries array. This array is needed for queries that have
	 * been executed before the id of the master object was ready. So this queries have to be updated
	 * with the actual ID. 
	 * @param string $table
	 * @param array $fixed
	 * @param string $id_field
	 */
	public function add_needid_query(string $table,array $fixed,string $id_field) {
	    $this->needid_queries[] = ['table'=>$table,'fixed'=>$fixed,'id_field'=>$id_field];
	}
	
	/**
	 * Processes all entries in the need_id_query
	 * @param \Sunhill\ORM\Storage\storage_base $storage
	 */
	protected function execute_need_id_queries(\Sunhill\ORM\Storage\storage_base $storage) {
	    $storage->entities['needid_queries'] = $this->needid_queries;
	    $storage->execute_need_id_queries();
	}
	
// ============================ Storagefunktionen =======================================	
	/**
	 * Liefert das aktuelle Storage zurück oder erzeugt eines, wenn es ein solches noch nicht gibt.
	 * @return \Sunhill\ORM\Storage\storage_base
	 */
	final protected function get_storage() {
	    return $this->create_storage();
	}
	
	/**
	 * Erzeugt ein Storage. Defaultmäßig ist es das mysql-Storage. Diese methode kann für Debug-Zwecke überschrieben werden
	 * @return \Sunhill\ORM\Storage\storage_mysql
	 */
	protected function create_storage() {
	    return new \Sunhill\ORM\Storage\storage_mysql($this);
	}
	    
// ================================ Loading ========================================	
	/**
	 * Checks, if the object with ID $id is in the cache. If yes, return it, othwerwise return false
	 * @param integer $id The id to search for
	 * @return bool|oo_object false if not in cache otherwise the cache entry
	 */ 	 
	protected function check_cache(int $id) {
	    if (Objects::is_cached($id)) {
	        return Objects::load($id);
	    }
	    return false;
	}
	
	/**
	 * Puts itself in the objects cache
	 * @param Int $id
	 */
	protected function insert_cache(int $id) {
	    Objects::insert_cache($id,$this);	    
	}
	
	/**
	 * Läd das Objekt aus dem Storage.
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\propertieshaving::do_load()
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
	protected function load_attributes(\Sunhill\ORM\Storage\storage_base $storage) {
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
	protected function load_external_hooks(\Sunhill\ORM\Storage\storage_base $storage) {
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
           $this->execute_need_id_queries($storage);
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
	
    /**
     * Removes the entry from the cache
     */
	protected function clear_cache_entry() {
	    Objects::clear_cache($this->get_id());
	}
	
	// ********************* Property handling *************************************	
	
	/**
	 * Recaculated all or one specific calculated fields. 
	 * If $property is set, only this one is recalculated
	 * @param unknown $property
	 */
	public function recalculate($property=null) {
	    if (!is_null($property)) {
	        $property_obj = $this->get_property($property);
	        $property_obj->recalculate();
	    } else {
	        $properties = $this->get_properties_with_feature('calculated');
	        foreach ($properties as $property) {
	            $property->recalculate();
	        }	        
	    }
	}
	
	/**
	 * Ruft für jede Property die durch $action definierte Methode auf und übergibt dieser das Storage
	 * @param string $action
	 * @param \Sunhill\ORM\Storage\storage_base $storage
	 */
	protected function walk_properties(string $action,\Sunhill\ORM\Storage\storage_base $storage) {
	    $properties = $this->get_properties_with_feature();
	    foreach ($properties as $property) {
	        $property->$action($storage);
	    }
	}

// ================================== Promotion ===========================================	
	
	/**
	 * Raises this object to a (higher) class
	 * @param String $newclass
	 * @return unknown
	 */
	public function promote(String $newclass) {
        return Objects::promote_object($this,$newclass);    
	}
	
	/**
	 * The old (lower) object is called before the promotion takes place.
	 * @param string $newclass
	 */
	public function pre_promotion(string $newclass) {
	    // Does nothing
	}
	
	/**
	 * The newly created (promoted) object is called after the promotion took place
	 * @param oo_object $from The old (lower) object
	 */
	public function post_promotion(oo_object $from) {
	    // Does nothing
	}

// ===================================== Degration =============================================	
	public function degrade(String $newclass) {
	    return Objects::degrade_object($this,$newclass);
	}
	
	public function pre_degration(string $newclass) {
	    
	}
	
	public function post_degration(oo_object $from) {
	    
	}

// =============================== Copying ====================================	
	/**
	 * This routine copies the properties to $newobject
	 * @param oo_object $newobject
	 */
	public function copy_to(oo_object $newobject) {
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
	 * This routine copies the properties of the $source to this object
	 * @param oo_object $source
	 */
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
	
	/**
	 * This function just calls the routine of the Classes facade
	 * @param boolean $full
	 * @return unknown
	 */
	public function get_inheritance($full=false) {
	    return Classes::get_inheritance_of_class(static::$object_infos['name'],$full);
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
	
	protected function set_external_hook($action,$subaction,$destination,$payload,$hook) {
	    parent::set_external_hook($action,$subaction,$destination,$payload,$hook);
        $this->get_property('externalhooks')->set_dirty(true);
	}
	
	public function array_field_new_entry($name,$index,$value) {
	    $this->check_for_hook('PROPERTY_ARRAY_NEW',$name,[$value]); 
	}
	
	public function array_field_removed_entry($name,$index,$value) {
	    $this->check_for_hook('PROPERTY_ARRAY_REMOVED',$name,[$value]);	    
	}
	
	protected function handle_unknown_property($name,$value) {
	    if ($attribute = \Sunhill\ORM\Properties\oo_property_attribute::search($name)) {
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
	        throw new \Sunhill\ORM\Properties\AttributeException("Das Attribut '".$attribute->name."' ist nicht für dieses Objekt erlaubt.");
	    }	    
	}
	
	/**
	 * This routine is called, whenever a migration on the class was performed
	 * @param unknown $added_fields
	 * @param unknown $removed_fields
	 */
	public function object_migrated(array $added_fields,array $removed_fields,array $changed_fields) {
	    
	}
	
	// ********************** Static methods  ***************************	
	
	/**
	 * Initializes the properties of this object. Any child has to call its parents setup_properties() method
	 */
	protected static function setup_properties() {
	    parent::setup_properties(); 
	    self::add_property('tags','tags')->searchable();
	    self::timestamp('created_at');
	    self::timestamp('updated_at');
	}

	// ****************** Migration **********************************
	/**
	 * @deprecated The migration should be done via Classes facade. This method is to be removed
	 */
	public static function migrate() {
        Classes::migrate_class(static::$object_infos['name']);
	}
	
	/**
	 * Traverses all classes in the hirachy and combines the static property $name in one resulting array
	 *
	 * @param string $name
	 * @return array
	 */
	public static function get_hirarchic_array(string $name)
	{
	    if (! property_exists(get_called_class(), $name)) {
	        throw new SunhillException("The property '$name' doesn't exists.");
	    }
	    $result = [];
	    $pointer = get_called_class();
	    do {
	        $result = array_merge($result, $pointer::$$name);
	        $pointer = get_parent_class($pointer);
	    } while (property_exists($pointer, $name)); // at least oo_object shouldn't define it
	    return $result;
	}
	
	
}