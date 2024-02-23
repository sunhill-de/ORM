<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\AbstractProperty;
use Sunhill\ORM\Tests\TestSupport\TestAbstractIDStorage;
use Sunhill\ORM\Properties\AbstractRecordProperty;

class NonAbstractRecordProperty extends AbstractRecordProperty
{
    public function isValid($test): bool
    {
        
    }
    
    
}

class AbstractRecordPropertyTest extends TestCase
{
     
}