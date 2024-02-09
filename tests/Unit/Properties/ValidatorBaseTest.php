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

class TestValidator extends ValidatorBase
{
    public function isValid($input): bool
    {
        return ($input == 'valid');
    }
    
    protected function doConvertToInput($input)
    {
        return '!'.$input.'!';
    }
}

class ValidatorBaseTest extends TestCase
{
 
    public function testIsValid()
    {
        $test = new TestValidator();
        $this->assertTrue($test->isValid('valid'));
        $this->assertFalse($test->isValid('invalid'));
    }
    
    public function testValidateInput()
    {
        $test = new TestValidator();
        $test->validateInput('valid');
        $this->expectException(InvalidValueException::class);
        $test->validateInput('invalid');
    }
    
    public function testconvertTopInput()
    {
        $test = new TestValidator();
        $this->assertEquals('!valid!', $test->convertToInput('valid'));
        $this->expectException(InvalidValueException::class);
        $test->convertToInput('invalid');
    }
}