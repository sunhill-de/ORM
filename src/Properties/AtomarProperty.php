<?php
/**
 * @file AtomarProperty.php
 * Defines a property that does not consist of other properties
 * Lang de,en
 * Reviewstatus: 2023-05-08
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Interfaces\InteractsWithStorage;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\Utils\Commitable;
use Sunhill\ORM\Properties\Utils\DefaultNull;

class AtomarProperty extends Property implements InteractsWithStorage, Commitable
{
    
// =============================== Class =======================================    
    /**
     * Stores the class of the property
     * @var string
     */
    protected $class = '';
    
    /**
     * Setter for $class
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setClass(string $class): Property
    {
        $this->class = $class;
        return $this;
    }
    
    /**
     * Getter for $class
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getClass(): string
    {
        return $this->class;
    }

// ========================== Default value handling ================================    
    /**
     * The default value for the value field. In combination with Property->defaults_null this default value
     * is used:
     * $default  | $defaults_null | Default value
     * ----------+----------------+------------------------------
     * not null  | any            | the value stored in $default
     * null      | true           | null
     * null      | false          | no default value
     * With a default value an property is never unititialized
     * @var void
     */
    protected $default;
    
    /**
     * See above
     * @var bool
     */
    protected $defaults_null = false;
    
    /**
     * Is this property allowed to take null as a value (by default yes)
     * @var boolean
     */
    protected $nullable = true;
    
    /**
     * sets the field Property->default (and perhaps Property->defaults_null too)
     *
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function setDefault($default): Property
    {
        if (!isset($default)) {
            $this->defaults_null = true;
        }
        $this->default = $default;
        return $this;
    }
    
    /**
     * Alias for setDefault()
     *
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function default($default)
    {
        return $this->setDefault($default);
    }
    
    /**
     * Returns the current default value
     *
     * @return null means no default value, DefaultNull::class means null is Default
     * otheriwse it return the default value
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getDefault()
    {
        if ($this->defaults_null) {
            return DefaultNull::class;
        }
        return $this->default;
    }
    
    /**
     * Is null the default value?
     *
     * @return boolean
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getDefaultsNull(): bool
    {
        return $this->defaults_null;
    }
    
    /**
     * Marks this property as nullable (null may be assigned as value). If there is
     * not already a default value, set null as default too
     *
     * @param bool $value
     * @return PropertyOld
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function nullable(bool $value = true): Property
    {
        $this->nullable = $value;
        if (!$this->defaults_null && !is_null($this->default)) {
            $this->default(null);
        }
        return $this;
    }
    
    /**
     * Alias for nullable()
     *
     * @param bool $value
     * @return PropertyOld
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function setNullable(bool $value = true): Property
    {
        return $this->nullable($value);
    }
    
    /**
     * Alias for nullable(false)
     *
     * @return PropertyOld
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function notNullable(): Property
    {
        return $this->nullable(false);
    }
    
    /**
     * Getter for nullable
     *
     * @return bool
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getNullable(): bool
    {
        return $this->nullable;
    }

    // ============================== Value Handling =====================================
    
    /**
     * The value of this property
     * @var void
     */
    protected $value;
    
    /**
     * The shadow value of this property. This is the value after the last Property->commit()
     * It is used for rollback and creation of the diff array (Property->getDiffArray())
     * @var void
     */
    protected $shadow;
    
    /**
     * Raises an expcetion when the property is readonly
     * @throws PropertyException
     */
    protected function checkReadonly()
    {
        if ($this->read_only) {
            throw new PropertyException("Write to a read only property.");
        }
    }
    
    protected function checkPermission()
    {
        
    }
    
    /**
     * If the property is already dirty, don't overwrite shadow
     */
    protected function handleShadow()
    {
        if (!$this->dirty) {
            $this->shadow = $this->value;
            $this->dirty = true;
        }
    }
    
    protected function handleValue($value)
    {
        if (is_null($value)) {
            if (!$this->nullable) {
                throw new PropertyException("Property is not nullable");
            }
        } else {
            $value = $this->validate($value);
        }
        $this->doSetValue($value);
    }
    
    protected function validate($input)
    {
        if (!$this->isValid($input)) {
            throw new PropertyException("The input value '$input' is invalid"); 
        }
        return $this->convertValue($input);
    }
    
    /**
     * Returns 
     * @param unknown $input
     * @return bool
     */
    public function isValid($input): bool
    {
        return true;    
    }
    
    public function convertValue($input)
    {
        return $input;
    }
    
    /**
     * Writes the value of this property
     * @param unknown $value
     * @param unknown $index
     * @throws PropertyException
     * @return \Sunhill\ORM\Properties\Property
     */
    final public function setValue($value)
    {
        $this->checkReadonly();
        $this->checkPermission();
        
        // Check if the value is really changed
        if ($this->initialized && ($value === $this->value)) {
            return $this;
        }
        $this->handleShadow();
        $this->handleValue($value);
        $this->initialized = true;
        return $this;
    }
    
    /**
     * Writes the new value
     * @param mixed $value
     */
    protected function doSetValue($value)
    {
        $this->value = $value;
    }
    
    final public function &getValue()
    {
        if (!$this->initialized) {
            if (isset($this->default) || $this->defaults_null) {
                $this->value = $this->default;
                $this->shadow = $this->default;
                $this->initialized = true;
            } else {
                if (!$this->initializeValue()) {
                    throw new PropertyException(__("Read of a not initialized property: ':name'",['name'=>$this->name]));
                }
            }
        }
        return $this->doGetValue();
    }
    
    /**
     * A last possibility to initialize a value (e.g. calculated field)
     * @return bool, true if successful otherwise false
     */
    protected function initializeValue(): bool
    {
        return false;
    }
    
    /**
     * Returns, if the property is initialized
     *
     * @return bool
     */
    public function getInitialized(): bool
    {
        return $this->initialized;
    }
    
    protected function &doGetValue()
    {
        return $this->value;
    }
    
    /**
     * Returns the value of the shadow field
     * @return void: The value of Property->shadow
     */
    public function getShadow()
    {
        return $this->shadow;
    }

    /**
     * Shows if the value was initialized at some time. If true that it was initialized already (even through
     * a default value or via loading). If false it was not initialied. A read access on a not initialized value
     * raises an excpetion.
     * @var bool
     */
    protected $initialized = false;
    
// ============================ Storage interaction =================================    
    public function storeToStorage(StorageBase $storage)
    {
        $storage->setEntity($this->getName(), $this->getValue());        
    }
    
    public function updateToStorage(StorageBase $storage)
    {
        $storage->setEntity($this->getName(), [
            'value'=>$this->getValue(), 
            'shadow'=>$this->getShadow()
        ]);
    }
    
    public function loadFromStorage(StorageBase $storage)
    {
        $name = $this->getName();
        $this->setValue($storage->$name);
        $this->setShadow($storage->$name);
        $this->setDirty(false);
    }

// =============================== Commitable =====================================    
    public function commit()
    {
        if ($this->isDirty()) {
            $this->setDirty(false);
            $this->shadow = $this->value;
        }
    }
    
    public function rollback()
    {
        if ($this->isDirty()) {
            $this->value = $this->shadow;
            $this->setDirty(false);
        }
    }
    
// ============================ GetAttributes =======================================
    public function getAttributes(): \StdClass
    {
        $result = parent::getAttributes();
        $result->class = $this->getClass();
        $result->default = $this->getDefault();
        $result->defaults_null = $this->getDefaultsNull();
        $result->nullable = $this->getNullable();
        $result->value = $this->value;
        $result->shadow = $this->shadow;
        return $result;
    }
}