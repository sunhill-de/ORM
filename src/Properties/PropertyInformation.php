<?php
/**
 * @file PropertyInformation.php
 * Provides the property for information fields
 * Lang en
 * Reviewstatus: 2023-06-22
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 * PSR-State: in progress
 */
namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Properties\Exceptions\WriteToReadonlyException;
use Sunhill\ORM\Properties\Exceptions\CalculatedCallbackException;

/**
 * The property class for information fields
 */
class PropertyInformation extends AtomarProperty 
{
	
	protected static $type = 'calculated';
	
	protected $info_path = '';
	
	protected $last_update = 0;
	
	/**
	 * A information field is always initialized
	 */
	protected function initializeValue(): bool 
	{
	    return true;
	}
	
	/**
	 * Calls the information market to retrieve the value
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\AtomarProperty::doGetValue()
	 */
	protected function &doGetValue()
	{
	}
	
	public function setPath(string $path): PropertyInformation
	{
	    $this->info_path = $path;
	    return $this;
	}
	
	public function getPath(): string
	{
	    return $this->info_path;
	}
}
