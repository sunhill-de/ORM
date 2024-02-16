<?php
/**
 * @file AbstractSimpleProperty.php
 * Defines an abstract property as base for all properties that are a simple type (not array, not record)
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Exceptions\InvalidValueException;
use Illuminate\Support\Facades\Log;

abstract class AbstractSimpleProperty extends AbstractProperty
{
    
    /**
     * Tries to pass a verbouse error message to the log
     *
     * @param string $message
     */
    protected function error(string $message)
    {
        if (empty($this->owner)) {
            Log::error($message);
        } else {
            Log::error($this->owner->getName().': '.$message);
        }
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
        
}