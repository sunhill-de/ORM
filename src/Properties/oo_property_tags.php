<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\TagException;
use Sunhill\ORM\Storage\storage_base;
use Sunhill\ORM\Facades\Tags;

/**
 * Diese Klasse repräsentiert die Property "Tags". Hier werden die Tags eines Objektes gespeichert.
 * @see Sunhill::Objects::Tag
 * @author lokal
 *
 */
class oo_property_tags extends PropertyArrayBase {
	
	protected $type = 'tags';
	
	protected $features = ['tags','array'];
	
	/**
	 * Legt fest, ob unbekannte Tags automatisch hinzugefügt werden sollen (true) oder eine
	 * Exception ausgelöst werden soll (false)
	 * @var bool
	 */
	protected $add_missing = false;
	
// ======================== Getter und Setter =================================	
	/**
	 * Setzt den Wert für "add_missing"
	 * @param boolean $value
	 */
	public function set_add_missing(bool $value) {
	   $this->add_missing = $value;    
	   return $this;
	}
	
	/**
	 * Gibt den Wert für add_missing zurück
	 * @return boolean
	 */
	public function get_add_missing() {
	    return $this->add_missing;
	}
	
// =========================== Hinzufügen und Löschen =======================================	
	/**
	 * Fügt ein neues Tag in die Liste ein
	 * @param Tag|int|string $tag
	 * @return void|\Sunhill\ORM\Properties\oo_property_tags
	 * @todo Hier könnte man auch über ein Lazy-Loading nachdenken, falls es überhaupt Performancegewinn bringt
	 */
	public function stick($tag) {
	    $tag = $this->getTag($tag);      // Das wahre Tag ermitteln
	    if ($this->is_duplicate($tag)) {  // Ist es schon in der Liste ?
	        return; // Ja, dann abbrechen
	    }
        if (!$this->get_dirty()) {
            $this->set_dirty(true);       // Und als dirty setzen
            $this->shadow = $this->value; // Schattenverzeichnis setzen
        }
        $this->value[] = $tag;  // Und an die Liste anfügen
	}
	
	/**
	 * Fügt ein neues Tag in die Liste ein
	 * @param Tag|int|string $tag
	 * @return void|\Sunhill\ORM\Properties\oo_property_tags
	 */
	public function remove($tag) {
        $tag = $this->getTag($tag);
    	    for ($i=0;$i<count($this->value);$i++) {
	        if ($this->value[$i]->get_id() === $tag->get_id()) {
	            if (!$this->get_dirty()) {
	                $this->set_dirty(true);       // Und als dirty setzen
	                $this->shadow = $this->value; // Schattenverzeichnis setzen
	            }
	            array_splice($this->value,$i,1);
	            return $this;
	        }	        
	    }	    
	    throw new PropertyException("Das zu löschende Tag '".$tag->get_fullpath()."' ist gar nicht gesetzt");
	}

	/**
	 * Tests if this tag is already in the list
	 * @param ORMObject $test
	 * @return boolean
	 */
	protected function is_duplicate(Tag $test) {
	    foreach ($this->value as $listed) {
	        if ($listed->get_id() == $test->get_id()) {
	            return $test;
	        }
	    }
	    return false;
	}
	
	/**
	 * Tests if the object is associatied with the given tag
	 * @param id|string|Tag $test the tag to test
	 * @return boolean
	 */
	public function HasTag($test) {
	    $tag_desc = Tags::findTag($test);
	    foreach ($this->value as $listed) {
	        if ($listed->get_id() == $tag_desc->id) {
	            return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * Ermittelt das Tag-Objekt zum übergebenen Tag
	 * Wenn $tag ein ORMObject ist, nur zurückgeben
	 * Wenn $tag ein int ist, Tag laden und zurückgeben
	 * Wenn $tag ein String ist, Tag suchen und in Abhängigkeit von add_missing hinzufügen oder nicht
	 * @param $tag Tag|int|string 
	 * @return Tag
	 */
	protected function getTag($tag) {
	    if (is_a($tag,Tag::class)) {
	        return $tag; // Trivial, ist bereits ein Objekt
	    } else if (is_int($tag)) {
	        return Tag::loadTag($tag); // Tag mit der ID laden
	    } else if (is_string($tag)) {
	        if ($this->add_missing) {
	            return Tag::search_or_add_tag($tag);
	        } else {
	            return Tag::searchTag($tag);
	        }
	    }
	    throw new TagException("Unbekannter Typ für ein Tag.");
	}

	
// ================================== Ermittlung des Wertes ====================================	
	protected function &do_get_indexed_value($index) {
	    $value = $this->value[$index]->get_fullpath();
	    return $value;
	}
	
	protected function do_insert(storage_base $storage,string $name) {
	    $result = [];
	    foreach ($this->value as $tag) {
	        if (is_int($tag)) {
	            $result[] = $tag;
	        } else {
	            $result[] = $tag->get_id();
	        }
	    }
	    $storage->set_entity('tags',$result);
	}
	
	protected function do_load(storage_base $loader,$name)  {
	    if (empty($loader->entities['tags'])) {
	        return;
	    }
	    foreach ($loader->entities['tags'] as $tag) {
	        $this->stick($tag);
	    }
	}
	
    /**
     * Überschriebene Methode, die bei Tags den Typ respektiert und zurück gibt
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\Property::get_diff_entry()
     */
	protected function get_diff_entry($tag,$type) {
	    switch ($type) {
	        case PD_ID: // Es wird immer die ID des Tags zurückgegeben
	            if (is_int($tag)) {
	                return $tag;
	            }
	            return $this->getTag($tag)->get_id();
	            break;
	        case PD_KEEP: // Es wird das zurückgegeben, was gerade geladen ist
	            return $tag;
	            break;
	        case PD_VALUE: // Es wird immer das Tag-Objekt zurückgegeben
	            return $this->getTag($tag);
	            break;
	    }
	}

	protected function NormalizeValue($value) {
	    if (is_a($value,Tag::class)) {
	        return $value->get_fullpath();
	    } else if (is_string($value)) {
	        return $value;
	    } else if (is_int($value)) {
	        $tag = Tags::loadTag($value);
	        return $tag->get_fullpath();
	    }
	}
	
	
}