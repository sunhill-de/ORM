<?php
/**
 * @file None.php
 * A unit class that indicates that this value has no unit (like a name)
 * Lang en
 * Reviewstatus: 2023-05-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Units/UnitTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Units;

class Kilometerperhour extends Unit
{
    
    protected static $name = 'Kilometerperhour';
    
    protected static $unit = 'km/h';
    
    protected static $basic_unit = Meterpersecond::class;
  
    protected static function doCalculateToBasic($input)
    {
        return $input * 1000 / 3600;
    }
    
    protected static function doCalculateFromBasic($input)
    {
        return $input / 1000 * 3600;
    }    
}
