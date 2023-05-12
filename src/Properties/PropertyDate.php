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

/**
 * A class for date properties
 */
class PropertyDate extends AtomarProperty 
{
	
    protected static $type = 'date';
	
    public function isValid($input): bool
    {
        return (bool)strtotime($input);
    }
    
}
