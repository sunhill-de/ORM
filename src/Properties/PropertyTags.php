<?php

/**
 * @file PropertyTags.php
 * Provides the tags property
 * Lang de,en
 * Reviewstatus: 2020-08-06
 * Localization: incomplete
 * Documentation: incomplete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\TagException;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Facades\Tags;
use Sunhill\Basic\Utils\Descriptor;

/**
 * Diese Klasse repräsentiert die Property "Tags". Hier werden die Tags eines Objektes gespeichert.
 * @see Sunhill::Objects::Tag
 * @author lokal
 *
 */
class PropertyTags extends PropertyArrayBase 
{
	
	protected static $type = 'tags';
	
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
	public function setAddMissing(bool $value) 
	{
	   $this->add_missing = $value;    
	   return $this;
	}
	
	/**
	 * Gibt den Wert für add_missing zurück
	 * @return boolean
	 */
	public function getAddMissing() 
	{
	    return $this->add_missing;
	}
	
// =========================== Hinzufügen und Löschen =======================================	
	/**
	 * Fügt ein neues Tag in die Liste ein
	 * @param Tag|int|string $tag
	 * @return void|\Sunhill\ORM\Properties\PropertyTags
	 * @todo Hier könnte man auch über ein Lazy-Loading nachdenken, falls es überhaupt Performancegewinn bringt
	 */
	public function stick($tag) 
	{
	    $tag = $this->getTag($tag);      // Das wahre Tag ermitteln
	    if ($this->isDuplicate($tag)) {  // Ist es schon in der Liste ?
	        return; // Ja, dann abbrechen
	    }
        if (!$this->getDirty()) {
            $this->setDirty(true);       // Und als dirty setzen
            $this->shadow = $this->value; // Schattenverzeichnis setzen
        }
        $this->value[] = $tag;  // Und an die Liste anfügen
	}
	
	/**
	 * Fügt ein neues Tag in die Liste ein
	 * @param Tag|int|string $tag
	 * @return void|\Sunhill\ORM\Properties\PropertyTags
	 */
	public function remove($tag) 
	{
        $tag = $this->getTag($tag);
    	    for ($i=0;$i<count($this->value);$i++) {
	        if ($this->value[$i]->getID() === $tag->getID()) {
	            if (!$this->getDirty()) {
	                $this->setDirty(true);       // Und als dirty setzen
	                $this->shadow = $this->value; // Schattenverzeichnis setzen
	            }
	            array_splice($this->value,$i,1);
	            return $this;
	        }	        
	    }	    
	    throw new PropertyException("Das zu löschende Tag '".$tag->getFullPath()."' ist gar nicht gesetzt");
	}

	/**
	 * Tests if this tag is already in the list
	 * @param ORMObject $test
	 * @return boolean
	 */
	protected function isDuplicate($test) 
	{
	    if (is_a($test,Tag::class)) {
	        $search_id = $test->getID();
	    } else if (is_a($test,Descriptor::class)) {
	        $search_id = $test->id;
	    }
	    foreach ($this->value as $listed) {
	        if ($listed->getID() == $search_id) {
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
	public function HasTag($test) 
	{
	    $tag_desc = Tags::findTag($test);
	    foreach ($this->value as $listed) {
	        if ($listed->getID() == $tag_desc->id) {
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
	protected function getTag($tag)
	{
	    if (is_a($tag,Tag::class)) {
	        return $tag; // Trivial, ist bereits ein Objekt
	    } else if (is_int($tag)) {
	        return Tags::loadTag($tag); // Tag mit der ID laden
	    } else if (is_string($tag)) {
	        if ($this->add_missing) {
	            return Tags::loadTag(Tags::searchOrAddTag($tag)->id);
	        } else {
	            $tag_desc = Tags::searchTag($tag);
	            return Tags::loadTag($tag_desc->id);
	        }
	    }
	    throw new TagException("Unbekannter Typ für ein Tag.");
	}

	
// ================================== Ermittlung des Wertes ====================================	
	protected function &doGetIndexedValue($index) 
	{
	    $value = $this->value[$index]->getFullPath();
	    return $value;
	}
	
	protected function doInsert(StorageBase $storage,string $name) 
	{
	    $result = [];
	    foreach ($this->value as $tag) {
	        if (is_int($tag)) {
	            $result[] = $tag;
	        } else {
	            $result[] = $tag->getID();
	        }
	    }
	    $storage->setEntity('tags',$result);
	}
	
	protected function doLoad(StorageBase $loader)  
	{
	    if (empty($loader->getEntity('tags'))) {
	        return;
	    }
	    foreach ($loader->getEntity('tags') as $tag) {
            $new_tag = new Tag();
            $new_tag->load($tag);
            $this->value[] = $new_tag;
	    }
	}
	
    /**
     * Überschriebene Methode, die bei Tags den Typ respektiert und zurück gibt
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\Property::getDiffEntry()
     */
	protected function getDiffEntry($tag,$type) 
	{
	    switch ($type) {
	        case PD_ID: // Es wird immer die ID des Tags zurückgegeben
	            if (is_int($tag)) {
	                return $tag;
	            }
	            return $this->getTag($tag)->getID();
	            break;
	        case PD_KEEP: // Es wird das zurückgegeben, was gerade geladen ist
	            return $tag;
	            break;
	        case PD_VALUE: // Es wird immer das Tag-Objekt zurückgegeben
	            return $this->getTag($tag);
	            break;
	    }
	}

	protected function NormalizeValue($value) 
	{
	    if (is_a($value,Tag::class)) {
	        return $value->getFullPath();
	    } else if (is_string($value)) {
	        return $value;
	    } else if (is_int($value)) {
	        $tag = Tags::loadTag($value);
	        return $tag->getFullPath();
	    }
	}

// ================================ Storage management ================================	
	public function storeToStorage(StorageBase $storage)
	{
	    $result = [];
	    foreach ($this->value as $tag) {
	        $result[] = $tag->getID();
	    }
	    $storage->setEntity('tags', $result);
	}
	
	public function updateToStorage(StorageBase $storage)
	{
	    $storage->setEntity($this->getName(), [
	        'value'=>$this->getValue(),
	        'shadow'=>$this->getShadow()
	    ]);
	}
	
	public function loadFromStorage(StorageBase $storage)
	{
	    if (empty($storage->getEntity('tags'))) {
	        return;
	    }
	    foreach ($storage->getEntity('tags') as $tag_id) {
	        $tag = new Tag();
	        $tag->load($tag_id);
	        $this->value[] = $tag;
	        $this->shadow[] = $tag;
	        $this->dirty = false;	        
	    }
	}
	
	protected function handleArrayValue($value)
	{
	    if (is_int($value)) {
	        $value = Tags::getTag($value);
	    } else if (is_string($value)) {
	        $value = Tags::searchTag($value);
	    }
	    if (is_a($value, Tag::class)) {
	       return $value;   
	    }
	    throw new PropertyException("Invalid value for a tag");
	}
	
	
}