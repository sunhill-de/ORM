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

	protected static $type = 'datetime';
	
	protected $initialized = true;
	
	
}