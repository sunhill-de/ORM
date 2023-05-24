<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\ObjectHandler;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;

class DummyObjectHandler extends ObjectHandler
{
    public $output = '';
    
    public function handleObject()
    {
        $this->output .= 'ORMObject';
    }
    
    protected function prepareRun()
    {
        $this->output = 'Output:';    
    }
    
    protected function finishRun()
    {
        $this->output .= 'Finished';
    }
    
    public function handlePropertyText(Property $property)
    {
        $this->output .= 'Text:'.$property->getName();
    }

    public function handlePropertyTime(Property $property)
    {
        $this->output .= 'Time:'.$property->getName();        
    }

    public function handlePropertyArray(Property $property)
    {
        $this->output .= 'Array:'.$property->getName();        
    }

    public function handlePropertyBoolean(Property $property)
    {
        $this->output .= 'Boolean:'.$property->getName();        
    }

    public function handlePropertyDateTime(Property $property)
    {
        $this->output .= 'DateTime:'.$property->getName();        
    }

    public function handlePropertyDate(Property $property)
    {
        $this->output .= 'Date:'.$property->getName();        
    }

    public function handlePropertyInteger(Property $property)
    {
        $this->output .= 'Integer:'.$property->getName();        
    }

    public function handlePropertyVarchar(Property $property)
    {
        $this->output .= 'Varchar:'.$property->getName();        
    }

    public function handlePropertyTimestamp(Property $property)
    {
        $this->output .= 'Timestamp:'.$property->getName();        
    }

    public function handlePropertyCalculated(Property $property)
    {
        $this->output .= 'Calculated:'.$property->getName();        
    }

    public function handlePropertyMap(Property $property)
    {
        $this->output .= 'Map:'.$property->getName();        
    }

    public function handlePropertyTags(Property $property)
    {
        $this->output .= 'Tags:';        
    }

    public function handlePropertyEnum(Property $property)
    {
        $this->output .= 'Enum:'.$property->getName();        
    }

    public function handlePropertyObject(Property $property)
    {
        $this->output .= 'Object:'.$property->getName();        
    }

    public function handlePropertyAttributes(Property $property)
    {
        $this->output .= 'Attributes:';        
    }

    public function handlePropertyFloat(Property $property)
    {
        $this->output .= 'Float:'.$property->getName();        
    }
    
}

class ObjectHandlerTest extends TestCase
{

    public function testDummy()
    {
        Classes::registerClass(Dummy::class);
        
        $class = new Dummy();
        $storage = new MysqlStorage($class);
        $test = new DummyObjectHandler($storage);
        
        $test->run();
        
        $this->assertEquals('Output:ORMObjectInteger:dummyintFinished', $test->output);
    }
    
    public function testDummyChild()
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        
        $class = new DummyChild();
        $storage = new MysqlStorage($class);
        $test = new DummyObjectHandler($storage);
        
        $test->run();
        
        $this->assertEquals('Output:ORMObjectInteger:dummychildintInteger:dummyintFinished', $test->output);
    }
}