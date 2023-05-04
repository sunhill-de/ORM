<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\StorageBase;

class PropertyAttribute extends Property {
	
	protected $type = 'attribute';
	
	protected $features = ['attribute','complex'];
	
	protected $allowed_objects;
	
	protected $property;
	
    protected $attribute_id;
    
    protected $attribute_name;

    protected $attribute_type;
    
    protected $attribute_property;
    
	public function initialize() 
	{
		$this->initialized = true;
	}

	public function setAllowedObjects(string $allowed_objects) 
	{
	    $this->allowed_objects = $allowed_objects;
	    return $this;
	}
	
	public function getAllowedObjects()
	{
	   return $this->allowed_objects;    
	}
	
	public function setAttributeID(int $id) 
	{
	    $this->attribute_id = $id;
	    return $this;
	}

	public function getAttributeID()
	{
	    return $this->attribute_id;
	}
	
	public function setAttributeName(string $name) 
	{
	    $this->attribute_name = $name;
	    return $this;
	}
	
	public function getAttributeName()
	{
	   return $this->attribute_name;    
	}
	
	public function setAttributeType(string $type) 
	{
	    $this->attribute_type = $type;
	    return $this;
	}
	
	public function getAttributeType()
	{
	   return $this->attribute_type;    
	}
	
	public function setAttributeProperty(string $property) 
	{
	    $this->attribute_property = $property;
	    return $this;
	}
	
// ===================================== Laden ===========================================	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\ORM\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function doLoad(StorageBase $loader,$name) 
	{	    
	    $this->attribute_name = $name;
	    $this->attribute_id = $loader->getEntity('attributes')[$name]->attribute_id;	    
	    $this->value = $this->extractValue($loader);
 	    $this->allowed_objects = $loader->getEntity('attributes')[$name]->allowed_objects;
	    $this->attribute_type = $loader->getEntity('attributes')[$name]->type;
	    $this->property = $loader->getEntity('attributes')[$name]->property;
	}
	
	/**
	 * Ermittelt den Wert des Attributs aus dem Storage. Defaultmäßig ist dies value, muss von Textattributen
	 * überschrieben werden.
	 * @param \Sunhill\ORM\Storage\StorageBase $loader
	 */
	protected function extractValue(StorageBase $loader) 
	{
	    return $this->value = $loader->getEntity('attributes')[$this->attribute_name]->value;    
	}

// ============================ Einfügen ========================================	
	protected function doInsert(StorageBase $storage,$name) 
	{
        $storage->getEntity('attributes')[$name] = [
            'name'=>$this->attribute_name,
            'attribute_id'=>$this->attribute_id,
            'allowed_objects'=>$this->allowed_objects,
            'type'=>$this->attribute_type,
            'property'=>$this->property
        ];
        $this->insertValue($storage);        
	}
	
	protected function insertValue(StorageBase $storage) 
	{
	    $storage->getEntity('attributes')[$this->attribute_name]['value'] = $this->value;    
	    $storage->getEntity('attributes')[$this->attribute_name]['textvalue'] = '';
	}
	
// ================================= Update =========================================
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
        $result = [
            'attribute_id'=>$this->attribute_id,
            'object_id'=>$this->owner->getID(),
            'name'=>'general_attribute',
            'allowedobjects'=>"\\Sunhill\\Objects\\ORMObject",
            'type'=>'int',
            'property'=>''            
        ];
        if ($this->attribute_type == 'text') {
            $result['textvalue']=['FROM'=>$this->shadow,'TO'=>$this->value];
            $result['value']=['FROM'=>'','TO'=>is_null($this->value)?null:''];            
        } else {
            $result['value']=['FROM'=>$this->shadow,'TO'=>$this->value];
            $result['textvalue']=['FROM'=>'','TO'=>is_null($this->value)?null:''];            
        }
        return $result;
	}
	
	public function doUpdate($storage, $name) {
	    $diff = $this->getDiffArray(PD_ID);
	    $storage->getEntity('attributes')[$this->attribute_name] = $diff;
	}
	
	// ============================ Statische Funktionen ===========================
	static public function search($name) {
	    $property = DB::table('attributes')->where('name','=',$name)->first();
	    if (empty($property)) {
	        return false;
	    } else {
	        return $property;
	    }
	}
}