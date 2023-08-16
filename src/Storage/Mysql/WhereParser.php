<?php

/**
 * @file WhereParser.php
 * Utility to parse the parameter passed to a collection/object query where function
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-08-13
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Query\TooManyWhereParametersException;
use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Query\WrongTypeException;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInformation;
use Sunhill\ORM\Properties\PropertyExternalReference;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyKeyfield;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyTags;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Query\NotAllowedRelationException;
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Objects\PropertiesCollectionException;

class WhereParser
{
    
    protected $tranlate_relation = [
        '=='=>'=',
        '!='=>'<>',
        'has'=>'any of',
    ];
    
    protected $relations = [
        '='=>[
            'arguments'=>'binary',
            'type'=>['scalar','string','boolean','date','time','datetime','primitive','array','map','object','collection'],
            'value'=>'native',
            'function'=>'handleWhereSimple'
        ],
        '<>'=>[
            'arguments'=>'binary',
            'type'=>['scalar','string','boolean','primitive','array','map','object','collection'],
            'value'=>'native'
        ],
        
        '<'=>['arguments'=>'binary','type'=>['scalar','string','date','time','datetime'],'value'=>'native'],
        '>'=>['arguments'=>'binary','type'=>['scalar','string','date','time','datetime'],'value'=>'native'],
        '<='=>['arguments'=>'binary','type'=>['scalar','string','date','time','datetime'],'value'=>'native'],
        '>='=>['arguments'=>'binary','type'=>['scalar','string','date','time','datetime'],'value'=>'native'],
        
        'in'=>[
            'arguments'=>'binary',
            'type'=>['scalar','string','primitive','object','collection'],
            'value'=>'array'
        ],
        'null'=>[
            'arguments'=>'unary',
            'type'=>['scalar','string','boolean','primitive','object','collection'],
            'value'=>'none'
        ],
        
        
        'contains'=>['arguments'=>'binary','type'=>['string','array','map','tags'],'value'=>'native'],
        'begins with'=>['arguments'=>'binary','type'=>['string'],'value'=>'native'],
        'ends with'=>['arguments'=>'binary','type'=>['string'],'value'=>'native'],
        
        'all of'=>['arguments'=>'binary','type'=>['array','map','tag'],'value'=>'array'],
        'any of'=>['arguments'=>'binary','type'=>['array','map','tag'],'value'=>'array'],
        'none of'=>['arguments'=>'binary','type'=>['array','map','tag'],'value'=>'array'],
        'empty'=>['arguments'=>'binary','type'=>['array','map','tag'],'value'=>'array'],
        'all keys of'=>['arguments'=>'binary','type'=>['array','map','tag'],'value'=>'array'],
        'any key of'=>['arguments'=>'binary','type'=>['map'],'value'=>'array'],
        'none key of'=>['arguments'=>'binary','type'=>['map'],'value'=>'array'],
        
        'has associations'=>['arguments'=>'none'],
        'is associated'=>['arguments'=>'none'],
        'has attributes'=>['arguments'=>'none'],
        'has tags'=>['arguments'=>'none'],
        
        'date'=>['arguments'=>'binary','type'=>['date','datetime'],'value'=>'native'],
        'time'=>['arguments'=>'binary','type'=>['time','datetime'],'value'=>'native'],
        'month'=>['arguments'=>'binary','type'=>['date','datetime'],'value'=>'native'],
        'day'=>['arguments'=>'binary','type'=>['date','datetime'],'value'=>'native'],
        'year'=>['arguments'=>'binary','type'=>['date','datetime'],'value'=>'native'],
    ];
    
    protected $class;
    
    protected $connection;
    
    protected $key;
    
    protected $relation;
    
    protected $value;
    
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }
    
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }
    
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    
    public function setRelation($relation)
    {
        $this->relation = $relation;
        return $this;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    /**
     * Check for unary conditions (the ones that don't expect an additional parameter)
     * @throws TooManyWhereParametersException
     * @return boolean|string[]|NULL[]
     */
    protected function checkUnaryRelation()
    {
        if ((!isset($this->relations[$this->key])) || ($this->relations[$this->key]['arguments'] <> 'none')) {
            return false;
        }
        if (!is_null($this->relation) || (!is_null($this->value))) {
            throw new TooManyWhereParametersException("With the relation '".$this->key."' no other parameters are allowed.");
        }
        switch ($this->key) {
            case 'has associations':
                return ['handleWhereHasAssociations', $this->connection];
                break;
            case 'is associated':
                return ['handleWhereIsAssociated', $this->connection];
                break;
            case 'has tags':
                return ['handleWhereHasTags', $this->connection];
                break;
            case 'has attributes':
                return ['handleWhereHasAttributes', $this->connection];
                break;
            default:
                return false;
        }
    }
    
    /**
     * This checks for specials keys that are no properties
     */
    protected function checkSpecialKey()
    {
        
    }
    
    protected function checkKey()
    {
        $class = $this->class;
        if ($class::definesProperty($this->key)) {
            return $class::getPropertyObject($this->key);
        }
        return false;
    }
    
    protected function tryToTranslateRelation()
    {
        if (isset($this->tranlate_relation[strtolower($this->relation)])) {
            $this->relation = $this->tranlate_relation[strtolower($this->relation)];
        }
    }
    
    protected function handleAttribute(\StdClass $attribute)
    {
        if (is_null($this->value)) { // Note: There can't be an attribute with the value null (this removes an attribute)
            $this->value = $this->relation;
            $this->relation = '=';
        } else {
            $this->tryToTranslateRelation();
        }
        if ($attribute->type == 'string') {
            if (!in_array('scalar',$this->relations[$this->relation]['type']) && !in_array('string',$this->relations[$this->relation]['type'])) {
                throw new \Sunhill\ORM\Query\NotAllowedRelationException();                
            }
        } else {
            if (!in_array('scalar',$this->relations[$this->relation]['type'])) {
                throw new \Sunhill\ORM\Query\NotAllowedRelationException();
            }
        }
        if ($this->relation == 'in') {
            if (!is_array($this->value)) {
                throw new WrongTypeException("The relation 'in' needs an array");
            }
            return ['handleWhereAttributeIn', $this->connection, $attribute, $this->value];            
        } else {
            return ['handleWhereAttributeSimple', $this->connection, $attribute, $this->relation, $this->value];
        }
    }
    
    protected function checkAttribute()
    {
        if (empty($all_attributes = Attributes::getAvaiableAttributesForClass($this->class))) {
            return false;   
        }
        foreach ($all_attributes as $attribute) {
            if ($attribute->name == $this->key) {
                return $this->handleAttribute($attribute);
            }
            return false;
        }
    }
    
    protected function getPropertyType(Property $property): string
    {
        switch ($property::class) {
            case PropertyArray::class:
                return 'array';
            case PropertyBoolean::class:
                return 'boolean';
            case PropertyEnum::class:
                return 'primitive';
            case PropertyCalculated::class:
                return 'string';
            case PropertyCollection::class:
                return 'collection';
            case PropertyDate::class:
                return 'date';
            case PropertyDatetime::class:
                return 'datetime';
            case PropertyExternalReference::class:
                return 'external';
            case PropertyFloat::class:
            case PropertyInteger::class:
                return 'scalar';
            case PropertyInformation::class:
                return 'information';
            case PropertyMap::class:
                return 'map';
            case PropertyObject::class:
                return 'object';
            case PropertyTags::class:
                return 'tags';
            case PropertyTime::class:
                return 'time';
            case PropertyKeyfield::class:
            case PropertyText::class:
            case PropertyVarchar::class:
                return 'string';
        }
    }
    
    protected function checkNativeValue($key)
    {
        if (!is_null($this->value) && !$key->isValid($this->value)) {
            throw new WrongTypeException("The given search value is not compatible with the type of field '".$key->getName()."'");
        }
    }
    
    protected function checkArrayValues($key)
    {
        if (!is_array($this->value)) {
            $this->value = [$this->value];
        }
        foreach ($this->value as $entry) {
            if (!is_null($entry) && !$key->isValid($entry)) {
                throw new WrongTypeException("The given search value is not compatible with the type of field '".$key->getName()."'");
            }            
        }
    }
    
    protected function checkValidValue($key, $relation)
    {
        if ($relation['value'] == 'native') {
            $this->checkNativeValue($key);
        } else if ($relation['value'] == 'array') {
            $this->checkArrayValues($key);
        }
    }

    protected function invertConnection()
    {
        switch ($this->connection) {
            case 'where':
                $this->connection = 'whereNot';
                break;
            case 'whereNot':
                $this->connection = 'where';
                break;
            case 'orWhere':
                $this->connection = 'orWhereNot';
                break;
            case 'orWhereNot':
                $this->connection = 'orWhere';
                break;
        }
    }
    
    protected function dispatchArray()
    {
        if (!is_array($this->value)) {
            $this->value = [$this->value];
        }
        switch ($this->relation) {
            case '=':
                return ['handleWhereArrayEquals', $this->connection, $this->key, $this->value];
                break;
            case '<>':
                $this->invertConnection();
                return ['handleWhereArrayEquals', $this->connection, $this->key, $this->value];
                break;
            case 'empty':
                return ['handleWhereEmpty', $this->connection, $this->key];
            case 'all of':
            case 'contains':    
                return ['handleWhereAllOf', $this->connection, $this->key, $this->value];
            case 'any of':
                return ['handleWhereAnyOf', $this->connection, $this->key, $this->value];
            case 'none of':
                return ['handleWhereNoneOf', $this->connection, $this->key, $this->value];
            case 'all keys of':
                return ['handleWhereAllKeysOf', $this->connection, $this->key, $this->value];
            case 'any key of':
                return ['handleWhereAnyKeyOf', $this->connection, $this->key, $this->value];
            case 'none key of':
                return ['handleWhereNoneKeyOf', $this->connection, $this->key, $this->value];
        }
    }
    
    protected function dispatchScalar()
    {
        switch ($this->relation) {
            case 'in':
                return ['handleWhereIn', $this->connection, $this->key, $this->value];
            case 'null':
                return ['handleWhereNull', $this->connection, $this->key];
            case 'begins with':
                return ['handleWhereLike', $this->connection, $this->key, $this->value.'%'];
            case 'ends with':
                return ['handleWhereLike', $this->connection, $this->key, '%'.$this->value];
            case 'contains':
                return ['handleWhereLike', $this->connection, $this->key, '%'.$this->value.'%'];
            case 'date':
            case 'time':
            case 'day':
            case 'month':
            case 'year':
                return ['handleWhereDateTime', $this->connection, $this->key, $this->relation, $this->value];
            default:
                return ['handleWhereSimple', $this->connection, $this->key, $this->relation, $this->value];
        }
    }
    
    protected function dispatchBoolean()
    {
        return ['handleWhereBoolean', $this->connection, $this->key, $this->relation, $this->value];    
    }
    
    protected function convertCollectionToID($input)
    {
        if (is_null($input)) {
            return null;
        }
        if (is_a($input, PropertiesCollection::class)) {
            return $input->getID();
        }
        if (is_numeric($input) && !is_float($input)) {
            return $input;
        }
        if (is_scalar($input)) {
            throw new WrongTypeException("The given input value '$input' could not be interpreted as a collection or object.");            
        } else {
            throw new WrongTypeException("The given input value could not be interpreted as a collection or object.");
        }
    }
    
    protected function dispatchCollection()
    {
        if (is_array($this->value)) {
            for($i=0;$i<count($this->value);$i++) {
                $this->value[$i] = $this->convertCollectionToID($this->value[$i]);
            }
        } else {
            $this->value = $this->convertCollectionToID($this->value);
        }

        if ($this->relation == 'in') {
            return ['handleWhereIn', $this->connection, $this->key, $this->value];            
        } else {
            return ['handleWhereSimple', $this->connection, $this->key, $this->relation, $this->value];
        }
    }
    
    protected function dispatchType($type)
    {
        switch ($type) {
            case 'array':
            case 'map':
                return $this->dispatchArray();
            case 'boolean':
                return $this->dispatchBoolean();
            case 'primitive':
            case 'string':
            case 'scalar':
            case 'date':
            case 'datetime':
            case 'time':
                return $this->dispatchScalar();
            case 'collection':
            case 'object':
                return $this->dispatchCollection();
            case 'external':
            case 'information':
            case 'tags':
        }
            
    }

    /**
     * Check if a statement like where('boolfield') or where('boolfield',true) instead of where('boolfield','=',true) was passed
     * @param unknown $key
     */
    protected function checkUnaryBoolean($key)
    {
        if (!is_a($key, PropertyBoolean::class)) {
            return;
        }
        if (is_bool($this->relation) && is_null($this->value)) {
            $this->value = $this->relation;
            $this->relation = '=';
        } else if (is_null($this->relation) && is_null($this->value)) {
            $this->value = true;
            $this->relation = '=';            
        }
    }
    
    /**
     * Check if a statement like where('somefield','somevalue') was passed instead of were('somefield','=','somevalue')
     * @throws NotAllowedRelationException
     */
    protected function checkDefaultRelation()
    {
        if (!isset($this->relations[strtolower($this->relation)])) {
            if (is_null($this->value)) {
                $this->value = $this->relation;
                $this->relation = '=';
            } else {
                throw new NotAllowedRelationException("The relation '".$this->relation."' is not implemented.");
            }
        }        
    }
    
    public function parseWhere(): array
    {
        if ($result = $this->checkUnaryRelation()) {
            return $result;
        }
        if ($result = $this->checkSpecialKey()) {
            return $result;
        }
        if (!($key = $this->checkKey())) {
            if ($result = $this->checkAttribute()) {
                return $result;
            }
            throw new UnknownFieldException();
        }
        $type = $this->getPropertyType($key);
        $this->checkUnaryBoolean($key);
        $this->tryToTranslateRelation();
        $this->checkDefaultRelation();
        $this->relation = strtolower($this->relation);
        
        $relation = $this->relations[$this->relation];
        if (!in_array($type, $relation['type'])) {
            throw new NotAllowedRelationException("The relation '".$this->relation."' is not allowed for the type '$type'");
        }
        $this->checkValidValue($key, $relation);
        return $this->dispatchType($type);
    }
    
}