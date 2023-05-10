<?php

namespace Sunhill\ORM\Properties;

class PropertyEnum extends AtomarProperty
{
	
	protected $type = 'string';
		
	protected $enum_values = [];
	
	public function setEnumValues(array $values): PropertyEnum 
	{
	    $this->enum_values = $values;
	    return $this;
	}
	
	public function getEnumValues() 
	{
	    return $this->enum_values;
	}
	
	public function setValues($values) 
	{
		$this->setEnumValues($values);
		return $this;
	}
}