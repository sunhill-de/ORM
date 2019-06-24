<?php

namespace Sunhill\Objects;

use App\tag;
use Illuminate\Support\Facades\DB;

define('TO_LEAFABLE',0x0001);

class TagException extends \Exception {}

class oo_tag extends \Sunhill\base {
		
	protected $model;
	
	protected $tag_id;
	
	protected $options = 0;
	
	protected $parent = null;
	
	protected $name = '';
	
	/**
	 * Konstruktor für ein Tag
	 * @param int|string|null $id
	 * Wenn $id ein Integer ist, wird das Tag mit dieser ID geladen
	 * Wenn $id ein String ist, wird ein passendes Tag zu diesem String gesucht
	 * Wenn $id null ist, wird ein leeres Tag angelegt
	 * @param boolean $autocreate Wenn true und die $id wurde nicht gefunden, wird ein Tag entsprechend angelegt
	 */
	public function __construct($id=null,$autocreate=false) {
		if (is_numeric($id)) {
			$this->load($id);
		} else if (is_string($id)) {
			$this->search($id,$autocreate);
		} else if (is_null($id)) {
		} else {
			// @todo Exeption werfen!
		}
	}
	
	/**
	 * Liefert die ID zurück
	 * @return number
	 */
	public function get_id() {
		if ($this->tag_id) {
			return $this->tag_id;
		} else {
			return 0;
		}
	}
	
	/**
	 * Liefert das Eltern-Tag oder null zurück
	 * @return \Crawler\oo_tag
	 */
	public function get_parent() {
		return $this->parent;
	}
	
	/**
	 * Setzt das Eltern-Tag
	 * @param oo_tag $parent
	 * @return \Crawler\oo_tag
	 */
	public function set_parent(oo_tag $parent) {
		$this->parent = $parent;
		return $this;		
	}
	
	/**
	 * Liefert den einfachen Namen des Tags zurück
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}
	
	/**
	 * Setzt den Namen des Tags
	 * @param string $name
	 * @return \Crawler\oo_tag
	 */
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	
	public function get_options() {
		return $this->options;
	}
	
	public function set_options($options) {
		$this->options = $options;
		return $this;
	}
	
	public function is_leafable() {
		return $this->options & TO_LEAFABLE;
	}
	
	public function set_leafable() {
		$this->options &= TO_LEAFABLE;
	}
	
	public function unset_leafable() {
		$this->options &= !TO_LEAFABLE;
	}
	
	/**
	 * Speichert das neue oder geänderte Tag ab
	 */
	public function commit() {
		if (!is_null($this->parent)) {
			$this->parent->commit();
		}
		if (!$this->get_id()) {
			$this->create();
		} else {
			$this->update();
		}
		self::flush_tagcache($this->get_id(),$this->get_fullpath());
	}
	
	/**
	 * Erzeugt ein neues Tag
	 */
	private function create() {
		if (isset($this->parent)) {
			$parent_id = $this->parent->get_id();
		} else {
			$parent_id = 0;
		}
	    $this->tag_id = DB::table('tags')->insertGetId([
	        'name'=>$this->name,
	        'parent_id'=>$parent_id,
    	    'options'=>$this->options,
	    ]);
	}
	
	/**
	 * Speichert das geänderte Tag
	 */
	private function update() {
	    if (isset($this->parent)) {
	        $parent_id = $this->parent->get_id();
	    } else {
	        $parent_id = 0;
	    }
	    DB::table('tags')->where('id',$this->tag_id)->update([
	        'name'=>$this->name,
	        'parent_id'=>$parent_id,
	        'options'=>$this->options,
	    ]);
	}
	
	/**
	 * Läd ein Tag aus der Datenbank
	 * @param int $id Die ID des Tags
	 */
	public function load($id) {
		$data = DB::table('tags')->where('id',$id)->first();
		if (is_null($data)) {
		    throw new \Exception("Tag mit der id '$id' nicht gefunden.");
		    return;
		}
		$this->options = $data->options;
		$this->name = $data->name;
		if ($data->parent_id) {
		    $this->parent = self::load_tag($data->parent_id);
		}
	}
	
	/**
	 * Liefert den vollständigen Pfad des Tags zurück (also eine Verknüpfung mit den Elterntags)
	 * @return string
	 */
	public function get_fullpath() {
		if (is_null($this->parent)) {
			return $this->get_name();
		} else {
			return $this->parent->get_fullpath().".".$this->get_name();
		}
	}
	
	/**
	 * 
	 * @param string $tag
	 * @param boolean $autocreate
	 */
	protected function search($tag,$autocreate) {
		$results = self::search_tag($tag);
		if (is_null($results)) {
		    if ($autocreate) {
		        return self::add_tag($tag);
		    } else {
		        // @todo Behandlung nicht gefundener Einträge ohne $autocreate
		        throw new TagException("Das Tag '$tag' wurde nicht gefunden.");
		    }		    
		}
		if (is_array($results)) {
		    throw new TagException("Das Tag '$tag' ist nicht eindeutig zuordbar.");
		    return false;    
		}
		return $results;
	}
	
    /**
     Löscht das Tag und alle Referenzen darauf
     */
	public function delete() {
	    $this->delete_references();
	    $this->delete_children();
	    $this->delete_this();
	}
	
	private function delete_references() {
	    DB::table('tagcache')->where('tag_id',$this->get_id())->delete();	    
	}
	
	private function delete_children() {
	    
	}
	
	private function delete_this() {
        DB::table('tagcache')->delete($this->tag_id);	    
	}
	// =============================== Statische Methoden ================================================
	/**
	 * Läd ein Tag mit der übergebenen ID
	 * Statischer Wrapper von oo_tag()->load()
	 * @param int $id
	 * @return \Sunhill\Objects\oo_tag
	 */
	public static function load_tag(int $id) {
	   $result = new oo_tag($id);
	   return $result;
	}
	
	public static function add_tag($tagname) {
	    $tag = new oo_tag();
	    $tag_components = explode('.',$tagname);
	    $tagname = array_pop($tag_components);
	    $tag->set_name($tagname)->set_options(TO_LEAFABLE);
	    if (!empty($tag_components)) { // Gibt es ein Eltern-Tag?
	        $glued = implode('.',$tag_components);
	        $tag->set_parent(new oo_tag($glued,true));
	    }
	    $tag->commit();
	    self::flush_tagcache($tag->get_id(),$tag->get_fullpath());
	    return $tag;
	}
	
	public static function delete_tag(string $tagname) {
	    $tag = self::search_tag($tagname);
	    if (is_null($tag)) {
	        throw new TagException("Tag '$tagname' nicht gefunden.");
	        return;
	    }
	    if (is_array($tag)) {
	        throw new TagException("Tag '$tagname' nicht eindeutig.");
	        return;	        
	    }
	    $tag->delete();
	}
	
	/**
	 * Sucht nach dem übergebenen Tag
	 * Gibt null zurück, wenn keines gefunden wurde
	 * Gibt das Tag zurück, wenn genau eines gefunden wurde
	 * Gibt ein Array von tags zurück, wenn mehrere gefunden wurden
	 * @param string $tag
	 * @return NULL|\Sunhill\Objects\oo_tag|\Sunhill\Objects\oo_tag[]
	 */
	public static function search_tag(string $tag) {
	    $results = DB::table('tagcache')->where('name','=',$tag)->get();
	    if (count($results) == 0) {
	        return null;
	    } else if (count($results) == 1) {
	        return self::load_tag($results[0]->tag_id);
	    } else {
          $return = array();
          foreach ($results as $result) {
              $return[] = self::load_tag($result->tag_id);
          }
          return $return;
	    }	    
	}
	
	public static function search_or_add_tag($tag) {
	   $tag = self::search_tag($tag);
	   if (is_null($tag)) {
	       $tag = self::add_tag($tag);
	   }
	   return $tag;
	}
	
	public static function flush_tagcache($id,$tagpath) {
	    DB::table('tagcache')->where('tag_id',$id)->delete();
	    $fullpath = explode('.',$tagpath);
	    while (!empty($fullpath)) {
	        DB::table('tagcache')->insert([
	            'name'=>implode('.',$fullpath),
	            'tag_id'=>$id
	        ]);
	        array_shift($fullpath);
	    }
	}
	
	public static function tree_tags($parent=null) {
	    
	}
	
	public static function get_orphaned_tags() {
	    
	}
}