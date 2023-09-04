<?php
/**
 * @file Semantic.php
 * The basic class for a semantic description of data
 * Lang de,en
 * Reviewstatus: 2023-05-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Semantic;

use Sunhill\ORM\Units\None;

class Semantic 
{
    protected static $name = 'Semantic';
    
    protected static $unit = 'None';
    
    /**
     * Getter for the name property
     * @return string
     */
    public static function getName(): string
    {
        return static::$name;
    }
    
    /**
     * Getter for the unit property
     * @return string
     */
    public static function getUnit(): string
    {
        return static::$unit;
    }
    
    public static function processValue($input)
    {
        return $input;    
    }
    
    /**
     * Semantic classes can process a value to make it human readable. For example the semantic 
     * length can add the unit to the value so the value reads "5 m". 
     * 
     * @param unknown $input
     * @return string
     */
    public static function processHumanReadableValue($input, string $unit): string
    {
        return empty($unit)?$input:$input.' '.$unit; // By default just append unit (if any)
    }
    
    protected static function translate(string $input): string
    {
        return $input;
    }
}
 