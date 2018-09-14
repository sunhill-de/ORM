<?php

namespace Sunhill\Objects;

use App\tag;

define('TO_LEAFABLE',0x0001);

class oo_tag extends \Sunhill\base {
	
	public $parent;
	
	protected $model;
	
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
			$this->model = new tag;
			$this->model->parent_id = 0;
			$this->model->options   = 0;
		} else {
			// @todo Exeption werfen!
		}
	}
	
	/**
	 * Liefert die ID zurück
	 * @return number
	 */
	public function get_id() {
		if ($this->model->id) {
			return $this->model->id;
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
		return $this->model->name;
	}
	
	/**
	 * Setzt den Namen des Tags
	 * @param string $name
	 * @return \Crawler\oo_tag
	 */
	public function set_name($name) {
		$this->model->name = $name;
		return $this;
	}
	
	public function get_options() {
		return $this->model->options;
	}
	
	public function set_options($options) {
		$this->model->options = $options;
		return $this;
	}
	
	public function is_leafable() {
		return $this->model->options & TO_LEAFABLE;
	}
	
	public function set_leafable() {
		$this->model->options &= TO_LEAFABLE;
	}
	
	public function unset_leafable() {
		$this->model->options &= !TO_LEAFABLE;
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
		$this->flush_tagcache();
	}
	
	private function flush_tagcache() {
		\App\tagcache::where('tag_id','=',$this->get_id())->delete();
		$fullpath = explode('.',$this->get_fullpath());
		while (!empty($fullpath)) {
			$entry = new \App\tagcache;
			$entry->name = implode('.',$fullpath);
			$entry->tag_id = $this->get_id();
			$entry->save();
			array_shift($fullpath);
		}
	}
	
	/**
	 * Erzeugt ein neues Tag
	 */
	private function create() {
		if (isset($this->parent)) {
			$this->model->parent_id = $this->parent->get_id();
		} else {
			$this->model->parent_id = 0;
		}
		$this->model->save();		
	}
	
	/**
	 * Speichert das geänderte Tag
	 */
	private function update() {
		$this->model->save();
	}
	
	/**
	 * Läd ein Tag aus der Datenbank
	 * @param int $id Die ID des Tags
	 */
	public function load($id) {
		$this->model = tag::where('id','=',$id)->first();
		if ($this->model->parent_id) {
			$this->parent = new oo_tag($this->model->parent_id);
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
		$results = \App\tagcache::where('name','=',$tag)->get();
		if (count($results) == 0) {
			if ($autocreate) {
				$this->create_tag($tag);
			} else {
				// @todo Behandlung nicht gefundener Einträge ohne $autocreate
				throw new \Exception("Das Tag '$tag' wurde nicht gefunden.");
			}
		} else if (count($results) == 1) {
			$this->load($results[0]->tag_id);
		} else {
			// @todo Behandlung uneindeutiger Einträge!
			throw new \Exception("Das Tag '$tag' ist nicht eindeutig zuordbar.");
		}
	}
	
	private function create_tag($tag) {
		$this->model = new tag;
		$tag_components = explode('.',$tag);
		$tag = array_pop($tag_components);		
		$this->set_name($tag)->set_options(TO_LEAFABLE);
		if (!empty($tag_components)) { // Gibt es ein Eltern-Tag?
			$glued = implode('.',$tag_components);
			$this->set_parent(new oo_tag($glued,true));
		}
		$this->commit();		
	}
}