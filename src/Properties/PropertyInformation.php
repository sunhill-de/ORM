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
use Sunhill\ORM\Properties\Utils\Cachable;

/**
 * The property class for information fields
 */
class PropertyInformation extends AtomarProperty 
{
	
    use Cachable;
    
	protected static $type = 'calculated';
	
	protected $info_path = '';
			
	public function setPath(?string $path): PropertyInformation
	{
	    $this->info_path = $path;
	    return $this;
	}
	
	public function getPath(): ?string
	{
	    return $this->info_path;
	}
	
	/**
	 * Get the value from the info market
	 */
	protected function retrieveValue()
	{
	
	}
	
}
