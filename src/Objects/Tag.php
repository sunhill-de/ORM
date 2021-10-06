<?php
/**
 * @file Tag.php
 * Provides the object Tag
 * Lang en
 * Reviewstatus: 2021-10-06
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: 
 */
namespace Sunhill\ORM\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Loggable;
use Sunhill\ORM\Facades\Objects;

define('TO_LEAFABLE',0x0001);

class Tag extends Loggable 
{
		
	protected $tag_id;
	
	protected $options = 0;
	
	protected $parent = null;
	
	protected $name = '';
		
	/**
	 * Returns the tag-ID
	 * @return number
	 */
	public function getID(): int
    {
		if ($this->tag_id) {
			return $this->tag_id;
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the parent tag or null if there is none
	 * @return Tag|null
	 */
	public function getParent(): ?Tag
    {
		return $this->parent;
	}
	
	/**
	 * Setzt das Eltern-Tag
	 * @param oo_tag $parent
	 * @return \Crawler\oo_tag
	 */
	public function setParent(Tag $parent): Tag
    {
		$this->parent = $parent;
		return $this;		
	}
	
	/**
	 * returns the simple name of the tag
	 * @return string
	 */
	public function getName(): string 
    {
		return $this->name;
	}
	
	/**
	 * Sets the simple name of the tag
	 * @param string $name
	 * @return Tag
	 */
	public function setName($name): Tag
    {
		$this->name = $name;
		return $this;
	}
	
	public function getOptions(): int
    {
		return $this->options;
	}
	
	public function setOptions(int $options): Tag 
    {
		$this->options = $options;
		return $this;
	}
	
	public function isLeafable(): bool
    {
		return $this->options & TO_LEAFABLE;
	}
	
	public function set_Leafable(): Tag
    {
		$this->options &= TO_LEAFABLE;
        return $this;
	}
	
	public function unsetLeafable(): Tag
    {
		$this->options &= !TO_LEAFABLE;
        return $this;
	}
	
	/**
	 * Saves the new or changed tag
	 */
	public function commit() 
    {
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
	private function create() 
    {
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
	private function update() 
    {
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
	public function load($id) 
    {
		$data = DB::table('tags')->where('id',$id)->first();
		if (is_null($data)) {
		    throw new \Exception("Tag mit der id '$id' nicht gefunden.");
		    return;
		}
		$this->options = $data->options;
		$this->name = $data->name;
		$this->tag_id = $id;
		if ($data->parent_id) {
		    $this->parent = self::load_tag($data->parent_id);
		}
	}
	
	/**
	 * Liefert den vollständigen Pfad des Tags zurück (also eine Verknüpfung mit den Elterntags)
	 * @return string
	 */
	public function get_fullpath() 
    {
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
	protected function search($tag,$autocreate)
    {
		$results = self::search_tag($tag);
		if (is_null($results)) {
		    if ($autocreate) {
		        $tag_obj = self::add_tag($tag);
		        $this->name = $tag_obj->get_name();
		        $this->parent = $tag_obj->get_parent();
		        $this->options = $tag_obj->get_options();
		        $this->tag_id = $tag_obj->get_id();
		    } else {
		        // @todo Behandlung nicht gefundener Einträge ohne $autocreate
		        throw new TagException("Das Tag '$tag' wurde nicht gefunden.");
		    }
		    return $tag_obj;
		}
		if (is_array($results)) {
		    throw new TagException("Das Tag '$tag' ist nicht eindeutig zuordbar.");
		    return false;    
		}
		$this->name = $results->getName();
		$this->parent = $results->getParent();
		$this->options = $results->getOptions();
		$this->tag_id = $results->getID();
		
		return $results;
	}
	
    /**
     * Deletes the tag and all references to it
     */
	public function delete(): null 
    {
	    $this->deleteReferences();
	    $this->deleteChildren();
	    $this->deleteThis();
	}
	
	private function deleteReferences(): null 
    {
	    DB::table('tagcache')->where('tag_id',$this->get_id())->delete();	    
	}
	
	private function deleteChildren(): null
    {
	    $entries = DB::table('tagobjectassigns')->where('tag_id',$this->getID())->get();
	    foreach ($entries as $entry) {
	        $this->deleteChild($entry->container_id);
	    }
	}
	
	private function deleteChild(int $object_id): null
    {
	    $object = Objects::load($object_id);
	    $object->tags->remove($this);
	    $object->commit();
	}
	
	private function deleteThis(): null
    {
	    DB::table('tags')->where('id',$this->getID())->delete();	    
	}
}
