<?php

/**
 * @file PropertyArray.php
 * The base class for arrays
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-16
 * Localization: no localization
 * Documentation: complete
 * Tests: Unit/Properties/ArrayPropertyTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

class PropertyArray extends PropertyArrayBase  
{
    
    protected static $type = 'array';
    
	protected $initialized = true;
	
	protected $pointer = 0;

}