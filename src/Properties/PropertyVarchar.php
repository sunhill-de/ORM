<?php

/**
 * @file PropertyVarchar.php
 * Provides the varchar property
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Properties;

class PropertyVarchar extends AtomarProperty 
{
	
	protected static $type = 'varchar';
	
	protected $maxlen = 255;
	
	/**
	 * Returns the maximum character size for this varchar
	 * @return int
	 */
	public function getMaxLen(): int 
	{
	    return $this->maxlen;
	}
	
	/**
	 * Sets the maximum character size for this varchar
	 * @param int $value
	 * @return Property
	 */
	public function setMaxLen(int $value): Property 
	{
	    $this->maxlen = $value;
	    return $this;
	}

	public function convertValue($input)
	{
	    return substr($input,0,$this->maxlen);
	}
	
}