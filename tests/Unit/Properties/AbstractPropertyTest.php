<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\AbstractProperty;
use Sunhill\ORM\Tests\TestSupport\TestAbstractIDStorage;
use Sunhill\ORM\Properties\Exceptions\NoStorageSetException;
use Sunhill\ORM\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\ORM\Tests\TestSupport\TestUserManager;
use Sunhill\ORM\Properties\Exceptions\NoUserManagerInstalledException;
use Sunhill\ORM\Properties\Exceptions\PropertyNotWriteableException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForWritingException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForModifyException;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;

class NonAbstractProperty extends AbstractProperty
{
    
    public $is_valid = true;
    
    public function __construct()
    {
        $this->setName('test_int');
    }
    
    public function getAccessType(): string
    {
        return 'integer';    
    }
    
    protected function formatForHuman($input)
    {
        return "A".$input;
    }
    
    public function isValid($input): bool
    {
        return $this->is_valid;
    }
    
    protected function doConvertToInput($input)
    {
        return 'Input'.$input;
    }
}

class AbstractPropertyTest extends TestCase
{
     
    public function testSetStorage()
    {
        $storage = new TestAbstractIDStorage();
        $storage->setID(1);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $this->assertEquals($storage, $test->getStorage());
        $this->assertEquals(345, $test->getValue());
    }
    /**
     * @dataProvider NamesProvider
     * @param unknown $test
     * @param bool $forbidden
     */
    public function testNames($name, bool $forbidden)
    {
        if ($forbidden) {
            $this->expectException(InvalidNameException::class);
        }
        $test = new NonAbstractProperty();
        
        $test->setName($name);
        
        $this->assertTrue(true);
    }
    
    public static function NamesProvider()
    {
        return [
            ['_forbidden', true],
            ['string', true],
            ['object', true],
            ['float', true],
            ['integer', true],
            ['boolean', true],
            ['collection', true],
            ['name_with_underscores', false],
            ['namewith1digit', false],
        ];    
    }
    
    
    public function testSetName()
    {
        $test = new NonAbstractProperty();
        $this->assertEquals('test_int', $test->getName());
        $test->setName('another');
        $this->assertEquals('another', $test->getName());
    }
    
    public function testForceName()
    {
        $test = new NonAbstractProperty();
        
        $test->forceName('_test');
        
        $this->assertEquals('_test', $test->getName());        
    }
    
    /**
     * @dataProvider AdditionalGetterProvider
     * @param unknown $item
     * @param unknown $value
     */
    public function testAdditionalGetter($item, $value)
    {
        $test = new NonAbstractProperty();
        $method = 'set_'.$item;
        $test->$method($value);
        $method = 'get_'.$item;
        $this->assertEquals($value, $test->$method());
    }
    
    public static function AdditionalGetterProvider()
    {
        return [
            ['test','TEST'],
            ['Test','TEST'],
            ['_Test','TEST']
        ];
    }
    
    public function testUnknownMethod()
    {
        $this->expectException(PropertyException::class);
        
        $test = new NonAbstractProperty();
        $test->unknownMethod();
    }
 
    public function testNoStorage()
    {
        $this->expectException(NoStorageSetException::class);
        $test = new NonAbstractProperty();
        
        $test->readCapability();
    }
    
    public function testGetCapabilities()
    {
        $storage = new TestAbstractIDStorage();
        $storage->setID(1);
        $storage->read_capability = 'read';
        $storage->write_capability = 'write';
        $storage->modify_capability = 'modify';
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        
        $this->assertEquals('read', $test->readCapability());
        $this->assertEquals('write', $test->writeCapability());
        $this->assertEquals('modify', $test->modifyCapability());
    }
    
    public function testPropertyNoReadable()
    {
        $storage = new TestAbstractIDStorage();
        $storage->is_readable = false;
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $this->expectException(PropertyNotReadableException::class);
        $test->getValue();
    }
    
    public function testNoUserManagerInstalled()
    {
        $storage = new TestAbstractIDStorage();
        $storage->read_capability = 'read';
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager('');
        
        $this->expectException(NoUserManagerInstalledException::class);
        $test->getValue();
    }
    
    public function testUserNotAuthorizedForReading()
    {
        $storage = new TestAbstractIDStorage();
        $storage->read_capability = 'read';
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $this->expectException(UserNotAuthorizedForReadingException::class);
        $test->getValue();
    }
    
    public function testUserAuthorizedForReading()
    {
        $storage = new TestAbstractIDStorage();
        $storage->read_capability = 'required';
        $storage->setID(1);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $this->assertEquals(345,$test->getValue());
    }
    
    public function testFormatForHuman()
    {
        $storage = new TestAbstractIDStorage();
        $storage->setID(1);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        
        $this->assertEquals('A345',$test->getHumanValue());        
    }
    
    public function testPropertyNoWriteable()
    {
        $storage = new TestAbstractIDStorage();
        $storage->is_writeable = false;
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        
        $this->expectException(PropertyNotWriteableException::class);
        $test->setValue(1234);
    }
    
    public function testNoUserManagerInstalledWhileWriting()
    {
        $storage = new TestAbstractIDStorage();
        $storage->write_capability = 'write';
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager('');
        
        $this->expectException(NoUserManagerInstalledException::class);
        $test->setValue(123);
    }
    
    public function testUserNotAuthorizedForWriting()
    {
        $storage = new TestAbstractIDStorage();
        $storage->write_capability = 'read';
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $this->expectException(UserNotAuthorizedForWritingException::class);
        $test->setValue(123);
    }
    
    public function testUserAuthorizedForWriting()
    {
        $storage = new TestAbstractIDStorage();
        $storage->write_capability = 'required';
        $storage->setID(1);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $test->setValue(123);
        $this->assertTrue(true);
    }
    
    public function testUserNotAuthorizedForModify()
    {
        $storage = new TestAbstractIDStorage();
        $storage->modify_capability = 'modify';
        $storage->setValue('test_int',455);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $this->expectException(UserNotAuthorizedForModifyException::class);
        $test->setValue(123);
    }    
    
    public function testUserAuthorizedForModify()
    {
        $storage = new TestAbstractIDStorage();
        $storage->modify_capability = 'required';
        $storage->setValue('test_int',455);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->setUserManager(TestUserManager::class);
        
        $test->setValue(123);
        $this->assertTrue(true);
    }

    public function testPropertyNoWriteableWhileModify()
    {
        $storage = new TestAbstractIDStorage();
        $storage->is_writeable = false;
        $storage->setValue('test_int',455);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        
        $this->expectException(PropertyNotWriteableException::class);
        $test->setValue(1234);
    }
    
    public function testDoSetValue()
    {
        $storage = new TestAbstractIDStorage();
        $storage->setID(1);
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        
        $test->setValue(123);
        $this->assertEquals('Input123', $storage->getValue('test_int'));
    }
    
    public function testValidateInput()
    {
        $storage = new TestAbstractIDStorage();
        $storage->setID(1);
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $test->is_valid = false;
        
        $this->expectException(InvalidValueException::class);
        
        $test->setValue(123);
    }
}