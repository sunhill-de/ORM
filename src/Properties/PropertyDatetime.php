<?php
/**
 * @file PropertyDatetime.php
 * Provides the property for datetime fields
 * Lang en
 * Reviewstatus: 2021-10-14
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 * PSR-State: in progress
 */
namespace Sunhill\ORM\Properties;

class PropertyDatetime extends PropertyField 
{

    protected $type = 'datetime';
	
    protected $features = ['object','simple'];
	
    protected $validator_name = 'datetime_validator';
    
}
