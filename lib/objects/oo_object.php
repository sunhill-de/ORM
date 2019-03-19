<?php

namespace Sunhill\Objects;

use App;

class ObjectException extends \Exception {}
class UnknownPropertyException extends ObjectException {}

class oo_object extends \Sunhill\propertieshaving {

	private $id;
		
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
	
	private $external_references = array();
	
	public function __construct() {
		parent::__construct();
		$this->tags = array();
		$this->tags_shadow = array();
		$this->check_for_hook('CONSTRUCTED');
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
		$this->tags_shadow = $this->tags;
		$this->check_for_hook('LOADED');
		return $result;
	}
	
	private function clean_properties() {
		foreach ($this->properties as $property) {
			$property->set_dirty(false);
		}
	}
	
	public function commit() {
	    if (!$this->is_committing()) { // Guard, um zirkuläres Aufrufen vom commit zu verhindern
	        $this->set_state('committing');
	        $this->check_for_hook('COMITTING');
	        if ($this->get_id()) {
	            $this->pre_update();
	            $this->check_for_hook('PREUPDATE');
    			$this->update();
    			$this->post_update(); 
    			$this->check_for_hook('POSTUPDATE');
    			$this->post_update_tags();
    		} else {
    		    $this->pre_create(); 
    		    $this->check_for_hook('PRECREATE');    		    
    			$this->create();
    			$this->post_create();
    			$this->check_for_hook('POSTCREATE');
    			$this->post_create_tags();
    		}
    		$this->comitted();
    		$this->check_for_hook('COMITTED');    		
    		$this->set_state('normal');
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
	    $readonly = $this->get_readonly();
	    $this->set_readonly(true);
	    $changed_fields = $this->get_changed_fields();
	    $broadcast = array();
		foreach ($changed_fields as $model=>$fields) {
		  foreach($fields as $field) {	
		    $property = $this->get_property($field);
			$method_name = $field.'_changed';
			if ($property->is_array()) {
			   $diff = $this->$field->get_array_diff();
			   if (method_exists($this, $method_name)) {
			      $this->$method_name($diff['NEW'],$diff['REMOVED']);
			   }
			   $this->check_for_hook('FIELDCOMMITTED', $field, array('arraychange'=>$diff));
			} else {
			    if (method_exists($this, $method_name)) {
			        $this->$method_name($property->get_old_value(),$property->get_value());
			    }
			    $this->check_for_hook('FIELDCOMMITTED',$field,array('from'=>$property->get_old_value(),
			                           'to'=>$property->get_value()));
			}
			$broadcast[$field] = array($property->get_old_value(),$property->get_value()); 
			$this->field_updated($field,$property->get_old_value(),$property->get_value());
		  }
		}
		if (!empty($broadcast)) { 
		    $this->broadcast_parents($broadcast,'update');
		}
		$this->set_readonly($readonly);
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
	            $this->check_for_hook('FIELDCOMMITTING',$field,array('from'=>$property->get_old_value(),
	                'to'=>$property->get_value()));
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
	}
		
	/**
	 * Diese Methode wird aufgrufen, um das Tag-Handling zu bewerkstelligen
	 * Sie speichert die Tag assoziationen in der Datenbank und ruft für jedes Tag tag_added auf
	 */
	private function post_create_tags() {
	}
	
	/**
	 * Diese Methode wird immer dann aufgerufen, wenn im Rahmen eines commit ein neues Tag mit dem Objekt assoziiert wurde
	 * @param oo_tag $tag Das Tag, welches assoziert wurde
	 */
	public function tag_added(oo_tag $tag) {
	    
	}
	
	/**
	 * Diese Methode wird immer dann aufgerufen, wenn im Rahmen eines commit ein Tag vom Objekt entfernt wurde
	 * @param oo_tag $tag Das Tag, welches assoziert wurde
	 */
	public function tag_removed(oo_tag $tag) {
	    
	}
		
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
	
	public function delete_tag(oo_tag $tag) {
	    for ($i=0;$i<count($this->tags);$i++) {
	        if ($this->tags[$i]->get_fullpath() === $tag->get_fullpath()) {
	            array_splice($this->tags,$i,1);
	            return $this; // Ende an dieser Stelle
	        }
	    }
        throw new ObjectException("Das zu löschende Tag '".$tag->get_fullpath()."' ist gar nicht gesetzt");
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
	    for ($i=0;$i<count($this->tags);$i++) {
	        $found = false;
	        for ($j=0;($j<count($this->tags_shadow));$j++) {
	            if ($this->tags[$i]->get_fullpath() == $this->tags_shadow[$j]->get_fullpath()) {
	                $found = true;
	            }
	        }	        
	        if (!$found) {
	            $result['added'][] = $this->tags[$i];
	        }
	    }
	    
	    for ($i=0;$i<count($this->tags_shadow);$i++) {
	        $found = false;
	        for ($j=0;($j<count($this->tags)) && (!$found);$j++) {
	            if ($this->tags_shadow[$i]->get_fullpath() === $this->tags[$j]->get_fullpath()) {
	                $found = true;
	            }
	        }
	        if (!$found) {
	            $result['deleted'][] = $this->tags_shadow[$i];
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
	
	protected function setup_properties() {
	    parent::setup_properties();
	    $this->timestamp('created_at')->set_model('coreobject');
	    $this->timestamp('updated_at')->set_model('coreobject');
	}
	public function __get($name) {
	    $this->check_for_hook('GET',$name,array(
	        'value'=>$this->properties[$name]->get_value()));
	    if (isset($this->properties[$name])) {
			return $this->properties[$name]->get_value();
		} else {
			return parent::__get($name);
		}
	}
	
	public function __set($name,$value) {
	    if (isset($this->properties[$name])) {
		    if ($this->get_readonly()) {
		        throw new \Exception("Property '$name' in der Readonly Phase verändert.");
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
		$property_name = '\Sunhill\Properties\oo_property_'.$type;
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
	    $model = \App\coreobject::where('id','=',$this->get_id())->first();
	    $model->classname = $newclass;
	    $model->save();
	    
	    return $newobject;
	}
	
	protected function copy_to(oo_object $newobject) {
	    $newobject->set_id($this->get_id());
	    foreach ($this->properties as $property) {
	        $name = $property->get_name();
	        switch ($property->get_type()) {
	            case 'array_of_objects':
	            case 'array_of_strings':
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
	    $model = \App\coreobject::where('id','=',$this->get_id())->first();
	    $model->classname = $newclass;
	    $model->save();
	    
	    return $newobject;
	    
	}
	
	public function copy_from(oo_object $source) {
	    $this->set_id($source->get_id());
	    foreach ($this->properties as $property) {
	        $name = $property->get_name();
	        switch ($property->get_type()) {
	            case 'array_of_objects':
	            case 'array_of_strings':
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
	
// ================================= Löschen =============================================	
	public function delete() {
	       $this->pre_delete();
           $this->deletion();
           unset(self::$objectcache[$this->get_id()]); // Cache-Eintrag löschen
	       $this->post_delete();
	}
	
	protected function pre_delete() {
	    
	}
	
	protected function deletion() {
	    $eraser = new oo_object_eraser($this);
	    return $eraser->erase();	    
	}
	
	protected function post_delete() {
	    
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
	    $this->add_hook('EXTERNAL','complex_changed',$field,$this,array('action'=>$action,'hook'=>$hook,'field'=>$restaction));
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
	
// ***************** Statische Methoden ***************************	
	
	private static $objectcache = array();
	
	/**
	 * Ermittelt den Klassennamen von dem Object mit der ID $id
	 * @param int $id ID des Objektes von dem der Klassennamen ermittelt werden soll 
	 * @return string Der Klassenname
	 */
	public static function get_class_name_of($id) {
	    $object = \App\coreobject::where('id','=',$id)->first();
	    if (empty($object)) {
	        return false;
	    }
	    return $object->classname;
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
	        self::$objectcache[$id] = $object;
	        $object->load($id);
	        return $object;
	    }
	}
	
	public static function flush_cache() {
	    self::$objectcache = array();
	}
}