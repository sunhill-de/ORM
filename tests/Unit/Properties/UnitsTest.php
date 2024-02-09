<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\AbstractProperty;
use Sunhill\ORM\Properties\ValidatorBase;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;

class UnitsTest extends TestCase
{

    protected function getUnits()
    {
        return include(dirname(__FILE__).'/../../../src/Properties/Units.php');
    }
    
    protected function calculate($item, $direction, $value)
    {
        $units = $this->getUnits();
        $field = 'calculate_'.$direction;
        $function = $units[$item][$field];
        if (!empty($function)) {
           return $function($value);
        }
        return $value;
    }
    
    /**
     * @dataProvider unitProvider
     */
    public function testUnit($name, $base, $unit, $base_unit)
    {
        $this->assertEquals($base_unit, round($this->calculate($name, 'to', $unit),2));
        $this->assertEquals($unit, round($this->calculate($name, 'from', $base_unit),2));
    }

    public static function unitProvider()
    {
        return [
            ['meter','meter',1,1],
            ['centimeter','meter',100,1],
            ['kilometer','meter',1,1000],
            ['millimeter','meter',1000,1],
            ['kilogramm','kilogramm',1,1],
            ['gramm','kilogramm',1000,1],
            ['degreecelsius','degreecelsius',1,1],
            ['degreekelvin','degreecelsius',300,26.85],
            ['degreefahrenheit','degreecelsius',99,37.22],
            ['meterpersecond','meterpersecond',1,1],
            ['kilometerperhour','meterpersecond',100.01,27.78]
        ];
    }
}