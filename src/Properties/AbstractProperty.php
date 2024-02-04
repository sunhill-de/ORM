<?php
/**
 * @file AbstractProperty.php
 * Defines an abstract property as base for all other properties
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\ORM\Properties\Exceptions\NoUserManagerInstalledException;

abstract class AbstractProperty
{
    
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
     */
    protected function checkName(string $name)
    {
        if (empty($name)) {
            throw new InvalidNameException("The property name '$name' must not be empty.");
        }
        if ($name[0] == '_') {
            throw new InvalidNameException("The property name '$name' must not start with an underscore.");
        }
        if (in_array(strtolower($name), Property::FORBIDDEN_NAMES)) {
            throw new InvalidNameException("The property name '$name' is reserved and not allowed.");
        }
    }
    
    /**
     * sets the field Property->name
     * @param $name The name of the property
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
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
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function name(string $name): AbstractProperty
    {
        return $this->setName($name);
    }
    
    /**
     * Returns the name of this property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
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
     */
    public static function setUserManager(string $user_manager)
    {
        self::$current_usermanager_fascade = $user_manager;    
    }
    
    /**
     * Returns the required capability to read this property or null if none is required
     * 
     * @return string|NULL
     */
    abstract public function readCapability(): ?string;
    
    /**
     * Returns true, when the property is readable
     * 
     * @return bool true, if the property is readable otherwise false
     */
    abstract public function isReadable(): bool;
    
    /**
     * Checks if this property is readable. If not it raises an exception
     * 
     * @throws PropertyNotReadableException::class When this property is not readbale
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
     * @throw 
     */
    private function checkIsAuthorizedForReading()
    {
       $capability = $this->readCapability();
       
       if (empty($capability)) {
           return; // If capability is empty, just leave
       }
       
       $this->doCheckReadCapability($capability);
    }
    
    protected function passOutputFilter($raw_value)
    {
        
    }
    
    abstract protected function doGetValue();
    
    public function getValue()
    {
        $this->checkIsReadable();
        $this->checkIsAuthorizedForReading();
        return $this->passOutputFilters($this->doGetValue());
    }
    
    protected function passHumanReadableFilters($value)
    {
        
    }
    
    public function getHumanReadableValue()
    {
        return $this->passHumanReadableFilters($this->getValue());
    }
    
}