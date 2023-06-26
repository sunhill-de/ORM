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
    
    public function handlePropertyText( $property)
    {
        $this->output .= 'Text:'.$property->getName();
    }

    public function handlePropertyTime( $property)
    {
        $this->output .= 'Time:'.$property->getName();        
    }

    public function handlePropertyArray( $property)
    {
        $this->output .= 'Array:'.$property->getName();        
    }

    public function handlePropertyBoolean( $property)
    {
        $this->output .= 'Boolean:'.$property->getName();        
    }

    public function handlePropertyDateTime( $property)
    {
        $this->output .= 'DateTime:'.$property->getName();        
    }

    public function handlePropertyDate( $property)
    {
        $this->output .= 'Date:'.$property->getName();        
    }

    public function handlePropertyInteger( $property)
    {
        $this->output .= 'Integer:'.$property->getName();        
    }

    public function handlePropertyVarchar( $property)
    {
        $this->output .= 'Varchar:'.$property->getName();        
    }

    public function handlePropertyTimestamp( $property)
    {
        $this->output .= 'Timestamp:'.$property->getName();        
    }

    public function handlePropertyCalculated( $property)
    {
        $this->output .= 'Calculated:'.$property->getName();        
    }

    public function handlePropertyMap( $property)
    {
        $this->output .= 'Map:'.$property->getName();        
    }

    public function handlePropertyTags( $property)
    {
        $this->output .= 'Tags:';        
    }

    public function handlePropertyEnum( $property)
    {
        $this->output .= 'Enum:'.$property->getName();        
    }

    public function handlePropertyObject( $property)
    {
        $this->output .= 'Object:'.$property->getName();        
    }

    public function handlePropertyAttributes( $property)
    {
        $this->output .= 'Attributes:';        
    }

    public function handlePropertyFloat( $property)
    {
        $this->output .= 'Float:'.$property->getName();        
    }
    
    public function handlePropertyCollection($property)
    {
        $this->output .= 'Collection'.$property->getName();
    }
    
    public function handlePropertyExternalReference($property)
    {
        $this->output .= 'External'.$property->getName();
        
    }
    
    public function handlePropertyInformation($property)
    {
        $this->output .= 'Information'.$property->getName();
        
    }
    
    public function handlePropertyKeyfield($property)
    {
        $this->output .= 'Keyfield'.$property->getName();
        
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