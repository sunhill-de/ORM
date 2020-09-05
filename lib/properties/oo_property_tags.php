<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\Objects\oo_tag;

/**
 * Diese Klasse repräsentiert die Property "Tags". Hier werden die Tags eines Objektes gespeichert.
 * @see Sunhill::Objects::oo_tag
 * @author lokal
 *
 */
class oo_property_tags extends oo_property_arraybase {
	
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
	 * @param oo_tag|int|string $tag
	 * @return void|\Sunhill\Properties\oo_property_tags
	 * @todo Hier könnte man auch über ein Lazy-Loading nachdenken, falls es überhaupt Performancegewinn bringt
	 */
	public function stick($tag) {
	    $tag = $this->get_tag($tag);      // Das wahre Tag ermitteln
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
	 * @param oo_tag|int|string $tag
	 * @return void|\Sunhill\Properties\oo_property_tags
	 */
	public function remove($tag) {
        $tag = $this->get_tag($tag);
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
	 * Testet, ob sich das Tag bereits in der Liste befindet
	 * @param \Sunhill\Objects\oo_object $test
	 * @return boolean
	 */
	protected function is_duplicate(\Sunhill\Objects\oo_tag $test) {
	    foreach ($this->value as $listed) {
	        if ($listed->get_id() == $test->get_id()) {
	            return $test;
	        }
	    }
	    return false;
	}
	
	/**
	 * Ermittelt das Tag-Objekt zum übergebenen Tag
	 * Wenn $tag ein oo_object ist, nur zurückgeben
	 * Wenn $tag ein int ist, Tag laden und zurückgeben
	 * Wenn $tag ein String ist, Tag suchen und in Abhängigkeit von add_missing hinzufügen oder nicht
	 * @param $tag oo_tag|int|string 
	 * @return oo_tag
	 */
	protected function get_tag($tag) {
	    if (is_a($tag,"\\Sunhill\\Objects\\oo_tag")) {
	        return $tag; // Trivial, ist bereits ein Objekt
	    } else if (is_int($tag)) {
	        return \Sunhill\Objects\oo_tag::load_tag($tag); // Tag mit der ID laden
	    } else if (is_string($tag)) {
	        if ($this->add_missing) {
	            return \Sunhill\Objects\oo_tag::search_or_add_tag($tag);
	        } else {
	            return \Sunhill\Objects\oo_tag::search_tag($tag);
	        }
	    }
	    throw new \Sunhill\Objects\TagException("Unbekannter Typ für ein Tag.");
	}

	
// ================================== Ermittlung des Wertes ====================================	
	protected function &do_get_indexed_value($index) {
	    $value = $this->value[$index]->get_fullpath();
	    return $value;
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
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
	
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name)  {
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
     * @see \Sunhill\Properties\oo_property::get_diff_entry()
     */
	protected function get_diff_entry($tag,$type) {
	    switch ($type) {
	        case PD_ID: // Es wird immer die ID des Tags zurückgegeben
	            if (is_int($tag)) {
	                return $tag;
	            }
	            return $this->get_tag($tag)->get_id();
	            break;
	        case PD_KEEP: // Es wird das zurückgegeben, was gerade geladen ist
	            return $tag;
	            break;
	        case PD_VALUE: // Es wird immer das Tag-Objekt zurückgegeben
	            return $this->get_tag($tag);
	            break;
	    }
	}
	
}