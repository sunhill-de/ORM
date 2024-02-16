<?php
/**
 * @file AbstractProperty.php
 * Defines an abstract property as base for all other properties
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Properties/AbstractPropertyTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\ORM\Properties\Exceptions\NoUserManagerInstalledException;
use Sunhill\ORM\Properties\Exceptions\PropertyNotWriteableException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForWritingException;
use Sunhill\ORM\Properties\Types\AbstractType;
use Sunhill\ORM\Storage\AbstractStorage;
use Sunhill\ORM\Properties\Exceptions\NoStorageSetException;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;
use Illuminate\Support\Facades\Log;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForModifyException;

abstract class AbstractProperty
{
    
    /**
     * Stores the current storage
     * 
     * @var unknown
     */
    protected $storage;
    
    /**
     * Setter for $storage
     * 
     * @param AbstractStorage $storage
     * @return \Sunhill\ORM\Properties\AbstractProperty
     * 
     * @test AbstractPropertyTest::testSetStorage()
     */
    public function setStorage(AbstractStorage $storage)
    {
        $this->storage = $storage;
        return $this;
    }
    
    /**
     * Getter for storage
     * 
     * @return AbstractStorage
     * 
     * @test AbstractPropertyTest::testSetStorage()
     */
    public function getStorage(): AbstractStorage
    {
        return $this->storage;
    }
    
    /**
     * Checks if a storage is set. If not raises an exception
     * 
     * @param string $action
     * @throes NoStorageExcveption
     * 
     * @test AbstractPropertyTest::testNoStorage()
     */
    protected function checkForStorage(string $action)
    {
        if (empty($this->storage)) {
            throw new NoStorageSetException("There is no storage set: $action");
        }
    }
    
// ====================================== Name =====================================================    
    /**
     * The name of this property
     * Property->getName() reads, Property->setName() writes
     * @var string
     */
    protected $_name = "";
    
    /**
     * A class constant for defining forbidden names for properties
     * @var array
     */
    const FORBIDDEN_NAMES = ['object','string','integer','float','boolean','collection', 'id', 'classname'];

    /**
     * Checks the designated name for this property if it is valid. If not is raises an exception
     * 
     * @param string $name
     * @exception InvalidNameException When the given name is not valid
     * 
     * @test AbstractPropertyTest::testNames()
     */
    protected function checkName(string $name)
    {
        if (empty($name)) {
            throw new InvalidNameException("The property name '$name' must not be empty.");
        }
        if ($name[0] == '_') {
            throw new InvalidNameException("The property name '$name' must not start with an underscore.");
        }
        if (in_array(strtolower($name), static::FORBIDDEN_NAMES)) {
            throw new InvalidNameException("The property name '$name' is reserved and not allowed.");
        }
    }
    
    /**
     * sets the field Property->name
     * @param $name The name of the property
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test Unit/Properties/PropertyTest::testNames()
     */
    public function setName(string $name): AbstractProperty
    {
        $this->checkName($name);
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Skips the name checking (for system properties)
     * @param string $name
     * @return Proeprty
     *
     * Test Unit/Properties/PropertyTest::forceNames()
     */
    public function forceName(string $name): AbstractProperty
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Alias for setName()
     *
     * @param string $name
     * @return PropertyOld
     *
     * Test Unit/Properties/PropertyTest::testNames
     */
    public function name(string $name): AbstractProperty
    {
        return $this->setName($name);
    }
    
    /**
     * Returns the name of this property
     *
     * Test Unit/Properties/PropertyTest::testNames
     */
    public function getName(): ?string
    {
        return $this->_name;
    }
    
    /**
     * Returns true, when the passed name is a valid property name otherwise false
     * 
     * @param string $test
     * @return bool
     *
     * Test Unit/Properties/PropertyTest::testNames
     */
    public function isValidPropertyName(string $test): bool
    {
        try {
            $this->checkName($test);
        } catch (InvalidNameException $e) {
            return false;
        }
        return true;
    }

// ==================================== Value handling ======================================    
    /**
     * A static variable that stores the current user manager (a facade or static interface
     * that implements a "hasCapability()" method
     * 
     * @var string
     */
    protected static $current_usermanager_fascade = '';
    
    /**
     * Initializes a user mangement interface for all properties. If any other user mangement than
     * the one from the sunhill framework is installed, here has to be an interface than defines the
     * hasCapability() method. This should return true when the current user has the given capability 
     * otherwise false. 
     * 
     * @param string $user_manager
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     */
    public static function setUserManager(string $user_manager)
    {
        self::$current_usermanager_fascade = $user_manager;    
    }
    
    /**
     * Returns the required capability to read this property or null if none is required
     * 
     * @return string|NULL
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     */
    public function readCapability(): ?string
    {
        $this->checkForStorage('readCapability');
        return $this->getStorage()->getReadCapability($this->getName());
    }
    
    /**
     * Returns true, when the property is readable
     * 
     * @return bool true, if the property is readable otherwise false
     * 
     * @test AbstractPropertyTest::testPropertyNotReadable
     */
    public function isReadable(): bool
    {
        $this->checkForStorage('isReadable');
        return $this->getStorage()->getIsReadable($this->getName());
    }
    
    /**
     * Checks if this property is readable. If not it raises an exception
     * 
     * @throws PropertyNotReadableException::class When this property is not readbale
     * 
     * @test AbstractPropertyTest::testPropertyNotReadable()
     */
    private function checkIsReadable()
    {
        if (!$this->isReadable()) {
            throw new PropertyNotReadableException("The property '".$this->_name."' is not readable.");
        }
    }

    /**
     * Checks if a user manager is installed. If yes it checks if the current user has the capability
     * to read this property
     * 
     * @param string $capability
     * @throws NoUserManagerInstalledException::class When no user manager is installed
     * @throws UserNotAuthorizedForReadingException::class When the current user is not authorized to read
     * 
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    private function doCheckReadCapability(string $capability)
    {
        if (empty(static::$current_usermanager_fascade)) {
            throw new NoUserManagerInstalledException("Property has a read restriction but no user manager is installed.");
        }
        if (!static::$current_usermanager_fascade::hasCapability($capability)) {
            throw new UserNotAuthorizedForReadingException("The current user is not authorized to read '".$this->_name."'");
        }
    }
    
    /**
     * Checks if this property has any restrictions for reading at all and if yes if the 
     * current user has this capability.
     * 
     * 
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    private function checkIsAuthorizedForReading()
    {
       $capability = $this->readCapability();
       
       if (empty($capability)) {
           return; // If capability is empty, just leave
       }
       
       $this->doCheckReadCapability($capability);
    }
    
    /**
     * Call this method before any reading attempts
     * 
     *  @test AbstractPropertyTest::testPropertyNotReadable()
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    protected function checkForReading()
    {
        $this->checkIsReadable();
        $this->checkIsAuthorizedForReading();        
    }
    
    /**
     * Performs the reading process
     * 
     *  @test AbstractPropertyTest::testPropertyNotReadable()
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    protected function doGetValue()
    {
        return $this->getStorage()->getValue($this->getName());
    }
    
    /**
     * Checks the reading restrictions and if passed performs the reading
     * 
     * @return unknown
     * 
     *  @test AbstractPropertyTest::testPropertyNotReadable()
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    public function getValue()
    {
        $this->checkForStorage('read');
        $this->checkForReading();
        return $this->doGetValue();
    }

    /**
     * Returns the value in a human readable format. The possible read restrictions are already
     * checked
     * @param unknown $input
     * @return unknown
     */
    protected function formatForHuman($input)
    {
        return $input;        
    }
    
    /**
     * Returns the value for saving in the storafge
     * 
     * @param unknown $input
     * @return unknown
     */
    protected function formatForStorage($input)
    {
        return $input;
    }
    
    /**
     * Returns the value as loaded from a storage
     * 
     * @param unknown $input
     * @return unknown
     */
    protected function formatFromStorage($input)
    {
        return $input;
    }
    
    /**
     * Returns the value in a human readable form 
     * 
     * @return \Sunhill\ORM\Properties\unknown
     * 
     * @tests AbstractPropertyTest::testFormatForHuman
     */
    public function getHumanValue()
    {
        $this->checkForStorage('read');
        $this->checkForReading();
        return $this->formatForHuman($this->doGetValue());
    }
    /**
     * Returns the required capability to read this property or null if none is required
     *
     * @return string|NULL
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     */
    public function writeCapability(): ?string
    {
        $this->checkForStorage('writeCapability');
        return $this->getStorage()->getWriteCapability($this->getName());
    }
    
    /**
     * Returns true, when the property is readable
     *
     * @return bool true, if the property is readable otherwise false
     * 
     * @test AbstractPropertyTest::testPropertyMotWriteable
     */
    public function isWriteable(): bool
    {
        $this->checkForStorage('isWriteable');
        return $this->getStorage()->getIsWriteable($this->getName());
    }
    
    /**
     * Returns true, when this property was already modified by an user. This is important for
     * a eventually existing modifyCapability
     * 
     * @return bool
     */
    public function isInitialized(): bool
    {
        $this->checkForStorage('isInitialized');
        return $this->getStorage()->getIsInitialized($this->getName());
    }
    
    /**
     * Returns the required capability to modify this property or null if none is required
     *
     * @return string|NULL
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     */
    public function modifyCapability(): ?string
    {
        $this->checkForStorage('modifyCapability');
        return $this->getStorage()->getModifyCapability($this->getName());
    }
    
    /**
     * Checks if this property is writeable. If not it raises an exception
     *
     * @throws PropertyNotWriteableException::class When this property is not writeable
     * @test AbstractPropertyTest::testPropertyNotWriteable
     */
    private function checkIsWriteable()
    {
        if (!$this->isWriteable()) {
            throw new PropertyNotWriteableException("The property '".$this->_name."' is not writeable.");
        }
    }
    
    /**
     * Checks if a user manager is installed. If yes it checks if the current user has the capability
     * to write this property
     *
     * @param string $capability
     * @throws NoUserManagerInstalledException::class When no user manager is installed
     * @throws UserNotAuthorizedForWritingException::class When the current user is not authorized to write
     * @test AbstractPropertyTest::testNoUserManagerInstalledWhileWriting()
     * 
     */
    private function doCheckWriteCapability(string $capability)
    {
        if (empty(static::$current_usermanager_fascade)) {
            throw new NoUserManagerInstalledException("Property has a read restriction but no user manager is installed.");
        }
        if (!static::$current_usermanager_fascade::hasCapability($capability)) {
            throw new UserNotAuthorizedForWritingException("The current user is not authorized to write '".$this->_name."'");
        }
    }
    
    /**
     * Checks if this property has any restrictions for writing at all and if yes if the
     * current user has this capability.
     *
     * @test AbstractPropertyTest::testUserNotAuthorizedForWriting()
     * @test AbstractPropertyTest::testUserAuthorizedForWriting()
     */
    private function checkIsAuthorizedForWriting()
    {
        $capability = $this->writeCapability();
        
        if (empty($capability)) {
            return; // If capability is empty, just leave
        }
        
        $this->doCheckWriteCapability($capability);
    }
    
    /**
     * Call this method before any writing attempts
     * 
     * @test AbstractPropertyTest::testPropertyNotWriteable
     * @test AbstractPropertyTest::testUserNotAuthorizedForWriting()
     * @test AbstractPropertyTest::testUserAuthorizedForWriting()
     */
    protected function checkForWriting()
    {
        $this->checkIsWriteable();
        $this->checkIsAuthorizedForWriting();
    }

    /**
     * Checks if a user manager is installed. If yes it checks if the current user has the capability
     * to write this property
     *
     * @param string $capability
     * @throws NoUserManagerInstalledException::class When no user manager is installed
     * @throws UserNotAuthorizedForWritingException::class When the current user is not authorized to write
     * @test AbstractPropertyTest::testUserAuthorizedForModify
     * @test AbstractPropertyTest::testUserNotAuthorizedForModify
     */
    private function doCheckModifyCapability(string $capability)
    {
        if (empty(static::$current_usermanager_fascade)) {
            throw new NoUserManagerInstalledException("Property has a read restriction but no user manager is installed.");
        }
        if (!static::$current_usermanager_fascade::hasCapability($capability)) {
            throw new UserNotAuthorizedForModifyException("The current user is not authorized to modify '".$this->_name."'");
        }
    }
    
    /**
     * Checks if this property has any restrictions for modifying at all and if yes if the
     * current user has this capability.
     *
     * @test AbstractPropertyTest::testUserAuthorizedForModify
     * @test AbstractPropertyTest::testUserNotAuthorizedForModify
     */
    private function checkIsAuthorizedForModify()
    {
        $capability = $this->modifyCapability();
        
        if (empty($capability)) {
            return; // If capability is empty, just leave
        }
        
        $this->doCheckModifyCapability($capability);
    }
    
    /**
     * Call this method before any modify attempts
     * @test AbstractPropertyTest::testUserAuthorizedForModify
     * @test AbstractPropertyTest::testUserNotAuthorizedForModify
     * @test AbstractPropertyTest::testPropertyNoWriteableWhileModify
     */
    protected function checkForModify()
    {
        $this->checkIsWriteable();
        $this->checkIsAuthorizedForModify();
    }
    
    /**
     * Converts the input to an defined value to store. For example an object is returned as
     * a object instance even if only a id is passed. By default this method just passes the input data
     *
     * @param unknown $input
     * @test AbstractPropertyTest::testDoSetValue
     */
    protected function doConvertToInput($input)
    {
        return $input;
    }
    
    /**
     * Performs the writing process
     *
     * @test AbstractPropertyTest::testDoSetValue  
     */
    protected function doSetValue($value)
    {
        $this->getStorage()->setValue($this->getName(), $this->doConvertToInput($value));
    }
    
    /**
     * Returns true, if the given value is accepted as an input value for this validator
     *
     * @param unknown $input The value to test
     * @return bool true if valid otherwise false
     * @test AbstractPropertyTest::testValidateInput  
     */
    abstract public function isValid($input): bool;
    
    /**
     * Checks if the given input value is acceptes, If not it raises an exception
     *
     * @param unknown $input
     * @throws InvalidValudException is thrown when the given valu is not valid
     * @test AbstractPropertyTest::testValidateInput  
     */
    protected function validateInput($input)
    {
        if (!$this->isValid($input)) {
            if (is_scalar($input)) {
                throw new InvalidValueException("The value '$input' is not valid.");
            } else {
                throw new InvalidValueException("The value is not valuid.");
            }
        }
    }
    
    /**
     * Checks the writing restrictions and if passed performs the writing
     *
     * @return unknown
     */
    public function setValue($value)
    {
        $this->checkForStorage('write');
        if ($this->isInitialized()) {
            $this->checkForModify();
        } else {
            $this->checkForWriting();
        }
        $this->validateInput($value);
        return $this->doSetValue($value);
    }
 
    public function commit()
    {
        $this->checkForStorage('commit');
        $this->getStorage()->commit();
    }
    
    public function rollback()
    {
        $this->checkForStorage('rollback');    
        $this->getStorage()->rollback();
    }
    
// *************************************** Metadata **********************************************

    /**
     * Returns the unique id string for the semantic of this property
     * 
     * @return string
     */
    public function getSemantic(): string
    {
        return 'none';
    }
    
    /**
     * Returns the unique id string for the unit of this property
     * 
     * @return string
     */
    public function getUnit(): string
    {
        return 'none';
    }
    
    /**
     * Returns ths suggested update frequency
     * 
     * The ressults mean:
     * - ASAP = No caching suggested, always request value directly
     * - second = A short time caching for a couple of seconds is possible
     * - minute = Cache for about one minutze
     * - hour = Cache for about one hour
     * - late = The value doesn't change often, update only from time to time
     * @return string
     */
    public function getUpdate(): string
    {
        return 'ASAP';    
    }
    
    /**
     * Returns the access type of this property. The access type is the hint in the metadata how
     * this property could be processed. The access type is not equivalent to the type of the property
     * 
     * Access type could be:
     * - string
     * - ingteger
     * - date
     * - datetime
     * - time
     * - float
     * - boolean
     * - array
     * - record
     * 
     * @return string
     */
    abstract public function getAccessType(): string;
    
    /**
     * Assembles the metadata of this property and returns them as a associative array
     * 
     * @return string[]
     */
    public function getMetadata()
    {
        $result = [];
        $result['semantic'] = $this->getSemantic();
        $result['unit'] = $this->getUnit();
        $result['type'] = $this->getAccessType();
        $result['update'] = $this->getUpdate();
        return $result;
    }
    
// ========================= Infomarket functionallity =========================================
    /**
     * Some atomar properties could have pseudo child elements (like count for arrays)
     * @param string $name
     * @return NULL
     */
    protected function requestTerminalItem(string $name)
    {
        return null;
    }
    
    /**
     * Try to pass the request to a child element. If none is found return null
     * @param string $name
     * @param array $path
     * @return NULL
     */
    protected function passItemRequest(string $name, array $path)
    {
        return null;
    }
    
    /**
     * When no path elements are left return $this, if only one is left check for
     * terminal item (pseudo child, see requestTerminalItem. Otherwise try to pass
     * The request to a child.
     * @param array $path
     * @return \Sunhill\ORM\Properties\Property|NULL
     */
    public function requestItem(array $path)
    {
        if (empty($path)) {
            return $this;
        }
        $next = array_shift($path);
        if (empty($path) && ($result = $this->requestTerminalItem($next))) {
            return $result;
        }
        return $this->passItemRequest($next, $path);
    }
    
    // ================================= Additional Fields ======================================
    /**
     * Properties get the possibility to add additinal fields (like property->set_additional)
     */
    private $additional_fields = [];
    
    /**
     * Extends the property with the possibility to deal with additional getters and setters
     *
     * @param string $method
     * @param array $params
     * @return mixed|NULL|\Sunhill\ORM\Properties\Property
     *
     * Test: /Unit/Properties/PropertyTest::testAdditionalGetter
     * Test: /Unit/Properties/PropertyTest::testUnknownMethod
     */
    public function __call(string $method, array $params)
    {
        if (substr($method,0,4) == 'get_') {
            $name = strtolower(substr($method,4));
            if (isset($this->additional_fields[$name])) {
                return $this->additional_fields[$name];
            } else {
                return null;
            }
        } else if (substr($method,0,4) == 'set_') {
            $name = strtolower(substr($method,4));
            $this->additional_fields[$name] = $params[0];
            return $this;
        }
        throw new PropertyException(static::class.": Unknown method '$method' called");
    }
            
}