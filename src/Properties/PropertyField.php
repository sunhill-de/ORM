<?php
/**
 * @file PropertyField.php
 * Provides an access to a single field of an object 
 * Lang en
 * Reviewstatus: 2021-10-14
 * Localization: none
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: complete
 * Dependencies: none
 */
namespace Sunhill\ORM\Properties;

/**
 * A basic property class for fields
 */
class PropertyField extends Property 
{
		
	protected $type;
	
	/**
	 * Sets the type of a field
	 * @return $this
	 */
	public function setType(string $type): Property 
	{
		$this->type = $type;
		return $this;
	}
	
	/**
	 * Returns the type of a field
	 * @return string
	 */
	public function getType(): string 
	{
		return $this->type;
	}
	
}
