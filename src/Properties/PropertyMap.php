<?php

/**
 * @file PropertyArray.php
 * The base class for arrays
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-16
 * Localization: no localization
 * Documentation: complete
 * Tests: Unit/Properties/ArrayPropertyTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

class PropertyMap extends PropertyArrayBase 
{
    
	protected $initialized = true;
	
	protected $pointer = 0;

	protected $maximum_key_length = 20;
	
	public function setMaxiumKeyLength(int $maximum)
	{
	    $this->maximum_key_length = $maximum;
	    return $this;
	}
}