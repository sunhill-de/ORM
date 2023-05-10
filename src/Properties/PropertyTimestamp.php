<?php
/**
 * @file PropertyTimestamp.php
 * Provides the timestamp property
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests: 
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;

class PropertyTimestamp extends AtomarProperty 
{

	protected $type = 'datetime';
	
	protected $initialized = true;
	
	/**
	 * Timestamps don't write to the storage, the storage has to take care of updating them
	 * 
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\AtomarProperty::storeToStorage()
	 */
	public function storeToStorage(StorageBase $storage)
	{
	}
	
	public function updateToStorage(StorageBase $storage)
	{
	}
	
	
}