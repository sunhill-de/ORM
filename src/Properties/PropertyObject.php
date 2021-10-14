<?php
/**
 * @file PropertyObject.php
 * Provides an access to a object field
 * Lang de,en
 * Reviewstatus: 2021-10-14
 * Localization: none
 * Documentation: incomplete
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: incomplete
 * Dependencies: LazyIDLoading
 */

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Properties\LazyIDLoading;

class PropertyObject extends PropertyField 
{
	
    use LazyIDLoading;
    
	protected $type = 'object';
	
	protected $features = ['object','complex','objectid'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	public function setAllowedObjects($object) 
	{
	    $this->validator->set_allowed_objects($object);
	    return $this;
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geladen wurde
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\Property::load()
	 */
	protected function doLoad(storage_base $storage, $name) 
	{
        $reference = $storage->$name;
	    if (!empty($reference)) {
	        $this->do_set_value($reference);
	    }
	}
	
	/**
	 * Überschriebene Methode von Property. Prüft, ob die Objekt-ID bisher nur als Nummer gespeichert war. Wenn ja, wird das
	 * Objekt lazy geladen.
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\Property::do_get_value()
	 */
	protected function &doGetValue() 
	{
	    if (is_int($this->value)) {
	        $this->value = Objects::load($this->value);
	    }
            return $this->value;	    
	}
	
	protected function doInsert(StorageBase $storage, string $name) 
	{
	    if (is_int($this->value)) {
	        $storage->setEntity($name,$this->value);
	    } else if (is_object($this->value)){
	        $storage->setEntity($name,$this->value->getID());
	    }
	}
	
	public function inserting(StorageBase $storage) 
	{
	    $this->commitChildIfLoaded($this->value);
	}

	public function inserted(StorageBase $storage) 
	{
	    $this->commitChildIfLoaded($this->value);	    
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
	        return [
	            'FROM'=>$this->getLocalID($this->shadow),
	            'TO'=>$this->getLocalID($this->value)
	        ];
	    } else {
	        return $diff;
	    }
	}
	
	public function updating(StorageBase $storage) 
	{
        $this->inserting($storage);
	}
	
	public function updated(StorageBase $storage) 
	{
	    $this->updating($storage);
	}
	
	protected function valueChanged($from, $to) 
	{
	    foreach ($this->hooks as $hook) {
	        $to->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }
	}
	
}
