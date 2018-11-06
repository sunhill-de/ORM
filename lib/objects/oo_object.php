<?php

namespace Sunhill\Objects;

use App;

class ObjectException extends \Exception {}
class UnknownPropertyException extends ObjectException {}

class oo_object extends \Sunhill\base {

	private $id;
	
	private $readonly=false;
	
	/**
	 * Speichert die Tags, die mit diesem Objekt assoziiert sind
	 * @var array of oo_tags
	 */
	protected $tags;
	
	protected $properties;
	
	public $default_ns = '\\App';
	
	/**
	 * Schattenspeicher für Tags, um im Falles eines rollbacks die Tags wiederherzustellen und die veränderten Tags zu ermitteln
	 */
	private $tags_shadow;
	
	private $comitting = false;
		
	public function __construct() {
		//parent::__construct();
		$this->tags = array();
		$this->tags_shadow = array();
		$this->setup_properties();
	}
	
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
	 * Läd das Objekt mit der ID $id aus der Datenbank
	 * @param integer $id
	 */
	public function load($id) {
		$loader = new oo_object_loader($this);
		$result = $loader->load($id); 
		$this->clean_properties();
		return $result;
	}
	
	private function clean_properties() {
		foreach ($this->properties as $property) {
			$property->set_dirty(false);
		}
	}
	
	public function commit() {
	    if (!$this->comitting) { // Guard, um zirkuläres Aufrufen vom commit zu verhindern
	        $this->comitting = true;
	        if ($this->get_id()) {
	            $this->pre_update(); 
    			$this->update();
    			$this->post_update(); 
    			$this->post_update_tags();
    		} else {
    		    $this->pre_create(); 
    			$this->create();
    			$this->post_create();
    			$this->post_create_tags();
    		}
    		$this->comitted();
    		$this->comitting = false;
	    } 
	}
	
	/**
	 * Ermittelt, welche Properties verändert wurden
	 * @return array of String
	 */
	public function get_changed_fields() {
		$result = array();
		foreach ($this->properties as $property) {
			if ($property->get_dirty()) {
				if (!isset($result[$property->get_model()])) {
					$result[$property->get_model()] = array($property->get_name());
				} else {
					$result[$property->get_model()][] = $property->get_name();
				}
			}
		}
		return $result;
	}
	
	/**
	 * Wird aufgerufen, wenn der commit ausgeführt wurde (egal ob create oder update)
	 */
	private function comitted() {
		$this->clean_properties();
		$this->tags_shadow = $this->tags; // Schattenverzeichnis überspielen
	}
	
	private function create() {
		$creator = new oo_object_creator($this);
		$id = $creator->store();
		$this->set_id($id);
		$this->update_children();
	}
	
	/**
	 * @todo unschöner Hack, update_children sollte privat bleiben und vom Objekt selber aufgerufen werden
	 */
	public function update_children() {
	    $fields = $this->get_complex_fields();
	    foreach ($fields as $fieldname) {
	        $property = $this->get_property($fieldname);
	        switch ($property->type) {
	            case 'object':
	                $object = $this->$fieldname;
	                if (!is_null($object)) {
	                    $object->commit();
	                }	                
	                break;
	            case 'array_of_objects':
	                foreach ($this->$fieldname as $object) {
	                    $object->commit();
	                }
	                break;
	        }
	    }	    
	}
	
	protected function pre_create() {
	}
	
	protected function post_create() {
		
	}
	
	private function update() {
	    $updater = new oo_object_updater($this);
		$updater->update();
	}
	
	protected function field_updated($fieldname,$oldvalue,$newvalue) {
		
	}
	
	/**
	 * Wird nach einem Datenbank update aufgerufen. Hier erfolgen die Verarbeitung von Triggern etc. 
	 */
	protected function post_update() {
		$this->readonly = true;
	    $changed_fields = $this->get_changed_fields();
	    $broadcast = array();
		foreach ($changed_fields as $model=>$fields) {
		  foreach($fields as $field) {	
			$property = $this->get_property($field);
			$method_name = $field.'_changed';
			if (method_exists($this, $method_name)) {
				$this->$method_name($property->get_old_value(),$property->get_value());
			}
			$broadcast[$field] = array($property->get_old_value(),$property->get_value()); 
			$this->field_updated($field,$property->get_old_value(),$property->get_value());
		  }
		}
		if (!empty($broadcast)) { 
		    $this->broadcast_parents($broadcast,'update');
		}
		$this->readonly = false;
	}
	
	/**
	 * Ruft die Elternobjekte auf und teilt ihnen mit, dass sich ein Kind geändert hat
	 * @param array $broadcast
	 * @param 'update' oder 'delete' $action
	 */
	private function broadcast_parents($broadcast,$action) {
	    $parents = \App\objectobjectassign::where('element_id','=',$this->get_id())->get();
	    foreach ($parents as $parent) {
	        $parent_object = self::load_object_of($parent->container_id);
	        $parent_object->child_changed($parent->field,$this,'update',$broadcast);
	    }	    
	}
	
	private function commit_child() {
	       
	}
	
	protected function pre_update() {
	    $this->update_children();
	    $changed_fields = $this->get_changed_fields();
	    foreach ($changed_fields as $model=>$fields) {
	        foreach($fields as $field) {
	            $property = $this->get_property($field);
	            $method_name = $field.'_changing';
	            if (method_exists($this, $method_name)) {
	                $this->$method_name($property->get_old_value(),$property->get_value());
	            }
	            $this->field_updated($field,$property->get_old_value(),$property->get_value());
	        }
	    }	    
	}
	
	/**
	 * Erzeugt ein leeres neues Objekt
	 */
	public function create_empty() {
		
	}
	
	// ***************************** Tag Handling **************************************
	
	/**
	 * Wird nach einem update aufgerufen, um die neuen und gelöschten Tags zu finden
	 */
	protected function post_update_tags() {
	    if (!empty($this->tags)) {
	        $added_tags = array_diff($this->tags,$this->tags_shadow);
	        foreach ($added_tags as $tag) {
	            $this->tag_added($tag);
	        }
	    }
	    if (!empty($this->tags_shadow)) {
	        $removed_tags = array_diff($this->tags_shadow,$this->tags);
	        foreach ($added_tags as $tag) {
	            $this->tag_removed($tag);
	        }
	    }
	}
		
	/**
	 * Diese Methode wird aufgrufen, um das Tag-Handling zu bewerkstelligen
	 * Sie speichert die Tag assoziationen in der Datenbank und ruft für jedes Tag tag_added auf
	 */
	private function post_create_tags() {
	    foreach ($this->tags as $tag) {
	        $this->tag_added($tag);
	    }
	}
	
	/**
	 * Diese Methode wird immer dann aufgerufen, wenn im Rahmen eines commit ein neues Tag mit dem Objekt assoziiert wurde
	 * @param oo_tag $tag Das Tag, welches assoziert wurde
	 */
	protected function tag_added(oo_tag $tag) {
	    
	}
	
	/**
	 * Diese Methode wird immer dann aufgerufen, wenn im Rahmen eines commit ein Tag vom Objekt entfernt wurde
	 * @param oo_tag $tag Das Tag, welches assoziert wurde
	 */
	protected function tag_removed(oo_tag $tag) {
	    
	}
	
/**	protected function tags_added($tags) {
		
	}
	
	protected function tags_deleted($tags) {
		
	}*/
	
	/**
	 * Fügt ein neues Tag hinzu oder ignoriert es, wenn es bereits hinzugefügt wurde
	 * @param oo_tag $tag
	 */
	public function add_tag(oo_tag $tag) {
		foreach ($this->tags as $listed) {
			if ($listed->get_fullpath() === $tag->get_fullpath()) {
				return $this; // Gibt es schon
			}
		}
		$this->tags[] = $tag;
		return $this;
	}
	
	/**
	 * Fügt ein Autotag hinzu
	 */
	public function add_auto_tag($tagstr) {
		$tagstr = 'autotag.'.$tagstr;
		$tag = new oo_tag($tagstr,true);
		$tag->commit();
		$this->add_tag($tag);
		return $this;
	}
	
	/**
	 * Ermittelt die Anzahl der assoziierten Tags
	 * @return integer
	 */
	public function get_tag_count() {
		return count($this->tags);
	}
	
	/**
	 * Ermittelt das $index-te Tag (Zählung beginnt bei 0)
	 * @param $index integer
	 * @return oo_tag
	 */
	public function get_tag($index) {
		return $this->tags[$index];	
	}

	/**
	 * Ermittelt, welche Tags hinzugefügt und welche gelöscht worden sind
	 * @return array[]
	 */
	public function get_changed_tags() {
	    $result = array('added'=>array(),'deleted'=>array());
	    $oldtags = $this->get_old_tags();
	    $newtags = $this->get_new_tags();
	    foreach ($this->tags as $tag) {
	        if (!in_array($tag->get_fullpath(),$oldtags)) {
	            $result['added'][] = $tag;
	        }
	    }
	    foreach ($oldtags as $tag) {
	        if (!in_array($tag->get_fullpath(),$newtags)) {
	            $result['deleted'][] = $tag;
	        }
	    }
	    return $result;
	}
	
	private function get_tags_only($source) {
	    $result = array();
	    foreach ($source as $tag) {
	        $result[] = $tag->get_fullpath();
	    }
	    return $result;
	}
	
	private function get_old_tags() {
	    return $this->get_tags_only($this->tags_shadow);
	}
	
	private function get_new_tags() {
	    return $this->get_tags_only($this->tags);
	}
	// ********************* Property Handling *************************************	
	
	/**
	 * Wird vom Constructor aufgerufen, um die Properties zu initialisieren.
	 * Abgeleitete Objekte müssen immer die Elternmethoden mit aufrufen.
	 */
	protected function setup_properties() {
	    $this->properties = array();
	    $this->timestamp('created_at')->set_model('coreobject');
	    $this->timestamp('updated_at')->set_model('coreobject');
	}
	
	public function __get($name) {
		if (isset($this->properties[$name])) {
			return $this->properties[$name]->get_value();
		} else {
			return parent::__get($name);
		}
	}
	
	public function __set($name,$value) {
		if (isset($this->properties[$name])) {
		    if ($this->readonly) {
		        throw new \Exception("Property '$name' in der Readonly Phase verändert.");
		    } else {
		          return $this->properties[$name]->set_value($value);
		    }
		} else {
			return parent::__set($name,$value);
		}		
	}
	
	/**
	 * Liefert das Property-Objekt der Property $name zurück
	 * @param string $name Name der Property
	 * @return oo_property
	 */
	public function get_property($name) {
	    if (!isset($this->properties[$name])) {
	        throw new UnknownPropertyException("Unbekannter Property '$property'");
	    }
	    return $this->properties[$name];
	}
	
	private function add_property($name,$type) {
		$property_name = '\Sunhill\Objects\oo_property_'.$type;
		$property = new $property_name($this);
		$property->set_name($name);
		$property->set_type($type);
		$this->properties[$name] = $property;
		return $property;
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
	 * Liefert die einfachen Felder sortiert nach Klassen (Models) zurück
	 * @return array[]
	 */
	public function get_simple_fields() {
		$models = array();
		foreach ($this->properties as $property) {
			if (!isset($models[$property->get_model()])) {
				$models[$property->get_model()] = array();
			}
			if ($property->is_simple()) {
				$models[$property->get_model()][] = $property->get_name();
			}
		}
		return $models;
	}
	
	/**
	 * Liefert die complexen Felder zurück
	 * @return array[]
	 */
	public function get_complex_fields() {
	    $result = array();
	    foreach ($this->properties as $property) {
	        if (!$property->is_simple()) {
	            $result[] = $property->get_name();
	        }
	    }
	    return $result;
	}

	/**
	 * Wird von untergebenen Objekte aufgerufen, wenn diese sich ändern, um die
	 * Eltern darüber zu informieren, dass sie verändert wurden
	 * @param $fieldname string Name des Feldes, dass sich geändert hat
	 * @param $childobject oo_object Das Objekt, welches sich ändert
	 * @param $action (update,delete), was mit diesem Objekt passiert
	 * @param $payload void, zusätzliche Parameter als Array
	 */
	public function child_changed($fieldname,$childobject,$action,$payload) {
	   $method_name = 'child_'.$fieldname.'_'.$action.'d';
	   if (method_exists($this, $method_name)) {
	       $this->$method_name($payload);
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
	    $object = \App\coreobject::where('id','=',$id)->first();
	    return $object->classname;
	}
	
	/**
	 * Erzeugt ein passendes Objekt zur übergebenen ID
	 * @param int $id ID des Objektes von dem ein Objekt erzeugt werden soll
	 * @return oo_object oder Abkömmling
	 */
	public static function load_object_of($id) {
	    if (isset(self::$objectcache[$id])) {
//	        echo "Cache!";
	        return self::$objectcache[$id];
	    } else {
	        $classname = self::get_class_name_of($id);
	        $object = new $classname();
	        self::$objectcache[$id] = $object;
	        $object->load($id);
	        return $object;
	    }
	}
	
	public static function flush_cache() {
	    self::$objectcache = array();
	}
}