<?php
/**
 * @file PropertyBoolean.php
 * Provides the property for boolean fields
 * Lang en
 * Reviewstatus: 2023-05-09
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: in progress
 */
namespace Sunhill\ORM\Properties;

/**
 * A class for boolean properties
 */
class PropertyBoolean extends AtomarProperty 
{
	
    protected static $type = 'bool';
	
    public function convertValue($input)
    {
        if (!is_string($input)) {
            return !empty($input);
        }
        return in_array(strtolower($input),[1,'1','y','true','+']);
    }
    
    public function isValid($input): bool
    {        
        if (!is_string($input)) {
            return true;
        }
        return in_array(strtolower($input),[0,1,'0','1','y','n','true','false','+','-']);
    }
    
}
