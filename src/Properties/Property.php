<?php
/**
 * @file Property.php
 * Defines a property
 * Lang de,en
 * Reviewstatus: 2023-05-08
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\Basic\Loggable;
use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Units\None;

class Property extends Loggable
{
    
    // ============================ Owner handling =====================================
    
    /**
     * This field stores the owner of this property. It points to an descendand of PropertyCollection
     * Property->getOwner() reads, Property->setOwner() writes
     * @var NonAtomarProperty
     */
    protected $owner;
    
    protected static $type = 'none';
    
    /**
     * sets the field Property->owner
     *
     * @param $owner a class of PropertyCollection
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test Unit/Properties/PropertyTest::testOwner
     */
    public function setOwner(?NonAtomarProperty $owner): Property
    {
        $this->owner = $owner;
        return $this;
    }
    
    /**
     * Alias for setOwner()
     *
     * @param PropertyOld $owner
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testOwner
     */
    public function owner(NonAtomarProperty $owner): Property
    {
        return $this->setOwner($owner);
    }
    
    /**
     * Returns the value of the owner field
     * @return PropertyCollection
     *
     * Test Unit/Properties/PropertyTest::testOwner
     */
    public function getOwner(): ?NonAtomarProperty
    {
        return $this->owner;
    }
    
    /**
     * Returns true if this property has an owner
     * 
     * @return bool
     */
    public function hasOwner(): bool
    {
        return !is_null($this->owner);    
    }
    
    // ******************************* Name handling ********************************
    /**
     * The name of this property
     * Property->getName() reads, Property->setName() writes
     * @var string
     */
    protected $name = "";
    
    /**
     * sets the field Property->name
     * @param $name The name of the property
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setName(string $name): Property
    {
        $this->name = $name;
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
    public function name(string $name): Property
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
        return $this->name;
    }
    
    // ============================== State-Handling ===========================================    
    /**
     * The state of a property indicates if the property is in some kind of processs (like loading
     * or even invalid)
     * @var string
     */
    protected $state = 'normal';
    /**
     * Sets the current state of this object
     * @param $state string the new state
     */
    protected function setState(string $state): Property
    {
        $this->state = $state;
        return $this;
    }
    
    /**
     * Returns the current state of this object
     * @return string
     */
    protected function getState(): string
    {
        return $this->state;
    }
    
    /**
     * Returns true if this object is comitting right now
     * @return bool
     */
    public function isCommitting(): bool
    {
        return ($this->getState() == 'committing');
    }
    
    /**
     * Returns true if this object is invalid
     * @return bool
     */
    public function isInvalid(): bool
    {
        return $this->getState() == 'invalid';
    }
    
    /**
     * Returns true if this object is loading right now
     * @return bool
     */
    public function isLoading(): bool
    {
        return $this->getState() == 'loading';
    }
    
    /**
     * Raises an exception if the property is invalid
     */
    protected function checkValidity()
    {
        if ($this->isInvalid()) {
            throw new PropertyException(__('Invalidated property called.'));
        }
    }
// =================================== Dirtybess ==========================================    
    /**
     * Shows if this property is dirty. If false the value wasn't change since initialization or the last
     * commit. If true than it was changed. An access should be performed via Property->getDirty() and
     * Property->setDirty().
     * @var bool
     */
    protected $dirty = false;
    
    /**
     * Returns true if this property is dirty
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->dirty;
    }
    
    public function getDirty(): bool
    {
        return $this->isDirty();
    }
    
    protected function setDirty(bool $value = true): Property
    {
        $this->dirty = $value;
        return $this;
    }
    
// ================================= Read only ============================================
    /**
     * Shows if the property is read only (true) or writable (false)
     * @var bool
     */
    protected $read_only = false;
    
    /**
     * Setter for $readonly
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setReadonly(bool $value = true): Property
    {
        $this->read_only = $value;
        return $this;
    }
    
    /**
     * Alias for setReadonly
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function readonly(bool $value = true): Property
    {
        return $this->setReadonly($value);
    }
    
    /**
     * Getter for $readonly
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getReadonly(): bool
    {
        return $this->read_only;
    }
    
// ============================== Unit ==================================== 
    /**
     * Does this property has a unit (by default none)
     * @var unknown
     */
    protected $unit = None::class;
 
    /**
     * Setter for unit
     * @param string $unit
     * @return PropertyOld
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setUnit(string $unit): Property
    {
        $this->unit = $unit;
        return $this;
    }
    
    /**
     * alias for setUnit
     * @param string $unit
     * @return Processor
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function unit(string $unit): Property
    {
        return $this->setUnit($unit);
    }
    
    /**
     * getter for unit
     * @return string
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getUnit(): string
    {
        return $this->unit;
    }
    
// =============================== Semantic =======================================
    /**
     * The semantic meaning of this property (by default name)
     * @var unknown
     */
    protected $semantic = Name::class;
    
    /**
     * Setter for sematic
     * @param string $sematic
     * @return PropertyOld
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setSemantic(string $semantic): Property
    {
        $this->semantic = $semantic;
        return $this;
    }
    
    /**
     * alias for setSematic
     * @param string $sematic
     * @return Processor
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function semantic(string $semantic): Property
    {
        return $this->setSemantic($semantic);
    }
    
    /**
     * getter for sematic
     * @return string
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getSemantic(): string
    {
        return $this->semantic;
    }

// =========================== Searchable ================================    
    /**
     * Shows if this property is searchable (true) or not (false)
     * @var bool
     */
    protected $searchable = false;

    /**
     * Setter for $searchable
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function setSearchable(bool $value = true): Property
    {
        $this->searchable = true;
        return $this;
    }
    
    /**
     * Alias for setSearchable()
     *
     * @param string $class
     * @return \Sunhill\ORM\Properties\Property
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function searchable(bool $value = true): Property
    {
        return $this->setSearchable($value);
    }
    
    /**
     * Getter for $earchable
     *
     * @return bool
     *
     * Test Unit/Properties/PropertyTest::testStandardGetters
     */
    public function getSearchable(): bool
    {
        return $this->searchable;
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
        if (substr($method,0,3) == 'get') {
            $name = strtolower(substr($method,3));
            if (isset($this->additional_fields[$name])) {
                return $this->additional_fields[$name];
            } else {
                return null;
            }
        } else if (substr($method,0,3) == 'set') {
            $name = strtolower(substr($method,3));
            $this->additional_fields[$name] = $params[0];
            return $this;
        }
        throw new PropertyException(static::class.": Unknown method '$method' called");
    }
    
 // ============================ GetAttributes =======================================
    public function getAttributes(): \StdClass
    {
        $result = new \StdClass();
        $result->owner = $this->getOwner();
        $result->name  = $this->getName();
        $result->state = $this->getState();
        $result->dirty = $this->getDirty();
        $result->readonly = $this->getReadonly();
        $result->unit  = $this->getUnit();
        $result->semantic = $this->getSemantic();
        $result->searchable = $this->getSearchable();
        $result->type = $this::getType();
        foreach ($this->additional_fields as $key => $value) {
            $result->$key = $value;
        }
        return $result;
    }
    
    public static function getType(): string
    {
        return static::$type;
    }
    
}