<?php

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Storage\Mysql\ColumnInfo;
use Sunhill\ORM\Traits\PropertyUtils;
use Sunhill\ORM\Tests\Testobjects\Dummy;

class PropertyUtilsTest extends DatabaseTestCase
{
    use PropertyUtils;
    
    public function testGetProperties()
    {
        $test = new Dummy();
        
        $list = $this->getAllProperties($test);
        
        $this->assertTrue(array_key_exists('dummyint',$list));
        $this->assertTrue(array_key_exists('uuid',$list));       
    }
    
    public function testGetPropertiesOnlyOwn()
    {
        $test = new Dummy();
        
        $list = $this->getAllProperties($test, true);
        
        $this->assertTrue(array_key_exists('dummyint',$list));
        $this->assertFalse(array_key_exists('uuid',$list));        
    }
}