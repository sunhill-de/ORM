<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;

class DummyCollectionHandler extends CollectionHandler
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

class CollectionHandlerTest extends TestCase
{

    public function testDummyCollection()
    {
        Classes::registerClass(Dummy::class);
        
        $class = new DummyCollection();
        $storage = new MysqlStorage($class);
        $test = new DummyCollectionHandler($storage);
        
        $test->run();
        
        $this->assertEquals('Output:Integer:dummyintFinished', $test->output);
    }
    
}