<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;

class PropertyArrayOfStrings extends PropertyArrayBase 
{
	
	protected $type = 'arrayOfStrings';
	
	protected $features = ['object','complex','array','strings'];
	
	public function setType($type) 
	{
	    $this->type = $type;
	    return $this;
	}
	
	public function getType() 
	{
	    return $this->type;
	}
		
}