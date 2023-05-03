<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;

class PropertyTest extends TestCase
{
 
    /**
     * @dataProvider StandardGettersProvider
     */
    public function testStandardGetters($setter, $getter, $value)
    {
        $test = new Property();
        $test->$setter($value);
        $this->assertEquals($value, $test->$getter());
    }
    
    public function StandardGettersProvider()
    {
        return [
            ['setName','getName','test'],
            ['name','getName','test'],
            
        ];
    }
}