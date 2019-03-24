<?php

namespace Sunhill\Objects;

use App;

class ObjectException extends \Exception {}
class UnknownPropertyException extends ObjectException {}

class oo_object extends \Sunhill\propertieshaving {

	public $default_ns = '\\App';
	
	private $external_references = array();
	
	
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
	    $model_name = $this->default_ns."\coreobject";
	    $model = $model_name::where('id','=',$this->get_id())->first();
	    $this->updated_at = $model->updated_at;
	    $this->created_at = $model->created_at;
	}
	
	private function load_simple_fields() {
	    $properties = $this->get_properties_with_feature('simple',null,'model');
	    foreach ($properties as $model_name => $fields_of_model) {
    	        if ($model_name == $this->default_ns."\coreobject") {
    	            continue; // Wurde schon geladen
    	        } 	           
    	        $model = $model_name::where('id','=',$this->get_id())->first();
    	        foreach ($fields_of_model as $field_name => $field) {
    	            $this->$field_name = $model->$field_name;
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
	    $simple_fields = $this->get_properties_with_feature('simple',null,'model');
        foreach ($simple_fields as $model_name => $fields_of_model) {
            $model = new $model_name();
            foreach ($fields_of_model as $field_name => $field) {
                $model->$field_name = $field->get_value();
            }
            if ($model_name == $this->default_ns."\coreobject") {
                continue; // Wurde schon gespeichert
            } else {
                $model->id = $this->get_id();
                $model->save();
            }
        }
	}
	
	private function insert_core_object() {
	       $model_name = $this->default_ns."\coreobject";
	       $model = new $model_name;
	       $model->classname = get_class($this);
	       $model->save();
	       $this->updated_at = $model->updated_at;
	       $this->created_at = $model->created_at;
	       $this->set_id($model->id);
	}

// ========================== Aktualisieren ===================================	
	protected function do_update() {
	    $this->update_core_object();
	    $simple_fields = $this->get_properties_with_feature('simple',true,'model');
	    foreach ($simple_fields as $model_name => $fields_of_model) {
	        $model = $model_name::where('id','=',$this->get_id())->first();
	        foreach ($fields_of_model as $field_name => $field) {
	            $model->$field_name = $field->get_value();
	        }
	        $model->save();
	    }
	}
	
	private function update_core_object() {
	    $model_name = $this->default_ns."\coreobject";
	    $model = $model_name::where('id','=',$this->get_id())->first();
	    $model->save();	    
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
	    \App\coreobject::destroy($this->get_id());
	}
	
	private function  delete_simple_fields() {
	    $fields = $this->get_properties_with_feature('simple');
	    foreach ($fields as $model_name=>$fields) {
	        if (!empty($model_name)) {
	            $model_name::destroy($this->get_id());
	        }
	    }
	}
	
	protected function clear_cache_entry() {
	    unset(self::$objectcache[$this->get_id()]); // Cache-Eintrag löschen
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
	protected function post_create_tags() {
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
	
	protected function get_tags() {
	    return $this->get_property('tags');
	}
	
	/**
	 * Fügt ein neues Tag hinzu oder ignoriert es, wenn es bereits hinzugefügt wurde
	 * @param oo_tag $tag
	 */
	public function add_tag(oo_tag $tag) {
		$this->get_tags()->add($tag);
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
	    return $this->get_property('tags')->get_array_diff();
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
	    $this->add_property('tags','tags');
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
	 * Diese Methode wird von $this->load() aufgerufen, wenn ein Objekt über den lader geladen wurde. Sie soll das Objekt in den Cache eintragen
	 * @param int $id
	 */
	protected static function load_id_called(int $id,oo_object $object) {
	    self::$objectcache[$id] = $object;
	}
	
	/**
	 * Erzeugt ein passendes Objekt zur übergebenen ID
	 * @param int $id ID des Objektes von dem ein Objekt erzeugt werden soll
	 * @return oo_object oder Abkömmling
	 */
	public static function &load_object_of($id) {
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