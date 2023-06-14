<?php
/**
 * @file PropertyDate.php
 * Provides the property for date fields
 * Lang en
 * Reviewstatus: 2021-10-14
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: in progress
 */
namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Utils\DateTimeCheck;

/**
 * A class for date properties
 */
class PropertyDate extends AtomarProperty 
{
	
    use DateTimeCheck;
    
    protected static $type = 'date';
	
        
}
