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

class PropertyTimestamp extends PropertyField 
{

	protected $type = 'timestamp';
	
	protected $features = ['object','complex'];

	protected $initialized = true;
}