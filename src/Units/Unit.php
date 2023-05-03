<?php
/**
 * @file Unit.php
 * A basic class for units (like meters, seconds, etc.)
 * Lang en
 * Reviewstatus: 2023-05-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Units/UnitTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Units;

abstract class Unit
{
    protected static $name = '';
    
    protected static $unit = '';
    
    protected static $basic_unit = null;
    
    public static function getName()
    {
        return static::$name;
    }
    
    public static function getUnit()
    {
        return static::$unit;
    }
    
    public static function calculateToBasic($input)
    {
        if (is_null(static::$basic_unit)) {
            return $input;
        }
        return static::doCalculateToBasic($input);
    }
    
    protected static function doCalculateToBasic($input)
    {
        return $input;
    }
    
    public static function calculateFromBasic($input)
    {
        if (is_null(static::$basic_unit)) {
            return $input;
        }
        return static::doCalculateFromBasic($input);
    }
    
    protected static function doCalculateFromBasic($input)
    {
        return $input;
    }
}
