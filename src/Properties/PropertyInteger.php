<?php

namespace Sunhill\ORM\Properties;

class PropertyInteger extends AtomarProperty {
	
	protected static $type = 'integer';

	/**
	 * Returns
	 * @param unknown $input
	 * @return bool
	 */
	public function isValid($input): bool
	{
	    return is_numeric($input);
	}
	
}