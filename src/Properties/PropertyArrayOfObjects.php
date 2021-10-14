<?php
/**
 * @file PropertyArrayOfObjects.php
 * A propertx that represents an array of objects
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Storage\StorageBase;

class PropertyArrayOfObjects extends PropertyArrayBase 
{

	protected $type = 'arrayOfObject';
		
	protected $features = ['object','complex','array','objectid'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	public function setAllowedObjects($object) 
	{
	    $this->validator->setAllowedObjects($object);
	    return $this;
	}

	public function setType($type) 
	{
	    $this->type = $type;
	    return $this;
	}
	
	public function getType() 
	{
	    return $this->type;
	}
	
	protected function NormalizeValue($value) 
	{
	    if (is_int($value)) {
	        return $value;
	    } else if (is_a($value,ORMObject::class)) {
	        return $value->getID();
	    }
	}
	
	protected function doLoad(StorageBase $loader, $name) 
	{
	    $references = $loader->$name;
	    if (empty($references)) {
	        return;
	    }
	    foreach ($references as $index => $reference) {
	       $this->value[$index] = $reference;
	    }
	}
	
	protected function &doGetIndexedValue($index) 
	{
	    if (is_int($this->value[$index])) {
	        $this->value[$index] = Objects::load($this->value[$index]);
	    }
	    return $this->value[$index];
	}
	
	protected function doInsert(StorageBase $storage, string $name) 
	{
	    $result = [];
	    foreach ($this->value as $index => $value) {
	        if (is_int($value)) {
	            $result[$index] = $value;
	        } else {
	            $result[$index] = $value->getID();
	        }
	    }
	    $storage->setEntity($name,$result);
	}
	
	public function inserting(StorageBase $storage) 
	{
	    if (!empty($this->value)) {
	        foreach ($this->value as $index=>$element) {
	            if (!is_int($element)) {
	                $element->commit();
	            } else if (Objects::isCached($element)) {
	                // Wenn es im Cache ist, kann es per seiteneffekt manipuliert worden sein
	                $this->value[$index] = Objects::load($element);	
	                $this->value[$index]->commit();
	            }
	        }
	    }
	}
	
	private function getLocalID($test) 
	{
	    if (is_null($test)) {
	        return null;
	    } else if (is_int($test)) {
	        return $test;
	    } else {
	        return $test->getID();
	    }
	}
	
	/**
	 * Erzeugt ein Diff-Array.
	 * d.h. es wird ein Array mit (mindestens) zwei Elementen zurückgebene:
	 * FROM ist der alte Wert
	 * TO ist der neue Wert
	 * @param int $type Soll bei Objekten nur die ID oder das gesamte Objekt zurückgegeben werden
	 * @return void[]|\Sunhill\ORM\Properties\Property[]
	 */
	public function getDiffArray(int $type = PD_VALUE) 
	{
	    $diff = parent::getDiffArray($type);
	    if ($type == PD_ID) {
	        $result = ['FROM'=>[],'TO'=>[],'ADD'=>[],'DELETE'=>[],'NEW'=>[],'REMOVED'=>[]];
	        foreach ($diff as $name=>$item) {
	            if (empty($item)) {
	                continue;
	            }
	            foreach ($item as $index=>$entry) {
	                $result[$name][$index] = $this->getLocalID($entry);
	            }
	        }
	        return $result;
	    } else {
	        return $diff;
	    }
	}
	
	public function updating(StorageBase $storage) 
	{
	    $this->inserting($storage);
	}
	
	protected function valueAdded($value) 
	{
	    foreach ($this->hooks as $hook) {
	        $value->addHook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }	    
	}
	
}