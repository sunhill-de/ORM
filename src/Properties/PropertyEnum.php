<?php

namespace Sunhill\ORM\Properties;

class PropertyEnum extends PropertyField 
{
	
	protected $type = 'enum';
	
	protected $features = ['object','simple'];
	
	protected $validator_name = 'EnumValidator';
	
	
	public function setEnumValues($values) 
	{
        $this->validator->setEnumValues($values);
	    return $this;
	}
	
	public function getEnumValues() 
	{
	    return $this->validator->getEnumValues();
	}
	
	public function setValues($values) 
	{
		$this->setEnumValues($values);
		return $this;
	}
}