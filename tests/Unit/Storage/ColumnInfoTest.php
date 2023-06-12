<?php

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Storage\Mysql\Utils\ColumnInfo;

class ColumnInfoTest extends DatabaseTestCase
{
    use ColumnInfo;    
 
    public function testGetColumnNames()
    {
        $test = $this->getColumnNames('dummies');
        $this->assertEquals(['id','dummyint'],$test);
    }
    
    public function testColumnExists()
    {
        $this->assertTrue($this->columnExists('dummies','dummyint'));
        $this->assertFalse($this->columnExists('dummies','nonexisting'));
    }
    
    public function testGetColumnType()
    {
        $this->assertEquals('integer',$this->getColumnType('dummies','dummyint'));
    }
    
    public function testGetColumnDefault()
    {
        $this->assertEquals(1,$this->getColumnDefault('testparents','nosearch'));
    }
    
    public function testGetColumnDefaultsNull()
    {
        $this->assertTrue($this->getColumnDefaultsNull('testparents', 'parentobject'));
        $this->assertEquals(null, $this->getColumnDefault('testparents', 'parentobject'));
    }
    
    public function testGetColumnLength()
    {
        $this->assertEquals(10, $this->getColumnLength('testparents','parentchar'));
    }
}