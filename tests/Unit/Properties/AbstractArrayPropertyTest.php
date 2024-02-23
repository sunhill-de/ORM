<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\AbstractArrayProperty;

class NonAbstractArrayProperty extends AbstractArrayProperty
{
     
    public function count(): int
    {
        return 2;
    }
    
    public function offsetExists(mixed $offset): bool
    {
        
    }
    
    public function offsetGet(mixed $offset): mixed
    {
        
    }
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        
    }
    
    public function offsetUnset(mixed $offset): void
    {
        
    }
    
    public function current(): mixed
    {
        
    }
    
    
    public function key(): mixed
    {
        
    }
    
    public function next(): void
    {
        
    }
    
    public function rewind(): void
    {
        
    }
    
    public function valid(): bool
    {
        
    }
    
    public function isValid($test): bool
    {
        
    }
    
}

class AbstractArrayPropertyTest extends TestCase
{
     
    public function testSimpleAccess()
    {
        $test = new NonAbstractArrayProperty();
        $this->assertEquals(2, $test->count());
    }
    
}