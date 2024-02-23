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

use Sunhill\ORM\Properties\Utils\DateTimeCheck;

class PropertyDatetime extends AtomarProperty 
{

    use DateTimeCheck;
    
    protected static $type = 'datetime';

    
    protected function sliceResult($input)
    {
        return $this->doSliceResult($input,true,true);
    }
    
}
