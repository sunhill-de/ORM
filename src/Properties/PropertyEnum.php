<?php

namespace Sunhill\ORM\Properties;

class PropertyEnum extends PropertyField 
{
	
	protected $type = 'enum';
	
	protected $features = ['object','simple'];
	
	protected $validator_name = 'enum_validator';
	
	
	public function setEnumValues($values) 
	{
        $this->validator->setEnumValues($values);
	    return $this;
	}
	
	public function getEnumValues() 
	{
	    return $this->validator->getEnumValues();
	}
	
	public function set_values($values) 
	{
		$this->setEnumValues($values);
		return $this;
	}
}