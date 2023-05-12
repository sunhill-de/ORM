<?php

namespace Sunhill\ORM\Properties;

class PropertyFloat extends AtomarProperty {
	
	protected static $type = 'float';
	
	public function isValid($input): bool
	{
	    return is_numeric($input);
	}
	
}