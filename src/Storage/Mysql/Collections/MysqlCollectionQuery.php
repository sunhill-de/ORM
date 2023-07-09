<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Query\DBQuery;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Query\NotAllowedRelationException;
use Sunhill\ORM\Query\NoUnaryConditionException;

class MysqlCollectionQuery extends DBQuery implements HandlesProperties
{
    protected $collection;
    
    public function __construct($collection)
    {
        $this->collection = $collection;
        parent::__construct();
    }
    
    protected function getBasicTable()
    {
        return DB::table($this->collection::getInfo('table'));
    }
    
    protected function handleWhere(string $connection, $key, $relation, $value)
    {
        if (is_null($value)) {
            if (is_null($relation)) {
                $relation = 'unary';   
            } else {
                $value = $relation;
                $relation = '=';
            }
        }
        $property = $this->collection->getProperty($key);
        if (empty($property)) {
            throw new UnknownFieldException("There is no field named '$key'");
        }
        $package = new \StdClass();
        $package->property = $property;
        $package->connection = $connection;
        $package->key = $key;
        $package->relation = $relation;
        $package->value = $value;
        $this->mapProperty($property, $package);
    }
   
    protected function isDynamicProperty($property): bool
    {
        return $this->collection->isDynamicProperty($property->name);
    }
    
    protected function mapProperty($property, $payload)
    {
        if ($this->isDynamicProperty($property)) {
            $this->handleAttribute($payload);
            return;
        }
        if (is_a($property,Property::class)) {
            $type_parts = explode('\\',$property::class);
        } else {
            $type_parts = explode('\\',$property->type);
        }
        $type = 'handle'.array_pop($type_parts);
        $this->$type($payload);
    }
        
    protected function checkRelation($package, $allowed_relations)
    {
        if (!in_array($package->relation, $allowed_relations)) {
            if ($package->relation == 'unary') {
                throw new NoUnaryConditionException("There is no unary condition for the field '".$package->key."'");                
            } else {
                throw new NotAllowedRelationException("The relation '".$package->relation."' is not allowed for the field '".$package->key."'");
            }
        }
    }
    
    protected function handleNullCondition($connection, $key)
    {
        $connection .= 'Null';
        $this->query->$connection($key);
    }
    
    protected function handleSimpleConditions($connection, $key, $relation, $value)
    {
        switch ($relation) {
            case 'unary':
                $this->handleNullCondition($connection, $key);
                break;
            case '=':
                if (is_null($value)) {
                    $this->handleNullCondition($connection, $key);
                } else {
                    $this->query->$connection($key, $relation, $value);
                }
                break;
            case '<>':
            case '!=':
            case '<':
            case '>':
            case '<=':
            case '>=':
                $this->query->$connection($key, $relation, $value);
                break;
        }        
    }
    
    public function handleAttribute($package)
    {
        
    }
    
    public function handlePropertyArray($property)
    {
        
    }
    
    public function handlePropertyBoolean($package)
    {
        $this->checkRelation($package, ['=','<>','!=','unary']);
        $connection = $package->connection;
        if ($package->relation == 'unary') {
            $this->query->$connection($package->key,'=',true);
        } else {
            $this->query->$connection($package->key,$package->relation,$package->value);
        }        
    }
    
    public function handlePropertyCalculated($property){}
    public function handlePropertyCollection($property){}
    public function handlePropertyDate($property){}
    public function handlePropertyDateTime($property){}
    public function handlePropertyEnum($property){}
    public function handlePropertyExternalReference($property){}
    
    public function handlePropertyFloat($package)
    {
        $this->checkRelation($package,['=','<>','!=','<','>','<=','>=','in']);
        $connection = $package->connection;
        if ($package->relation == 'in') {
            $this->handleInStatement($connection, $package->key, $package->value);
        } else {
            $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
        }        
    }
    
    public function handlePropertyInformation($property){}

    public function handlePropertyInteger($package)
    {
        $this->checkRelation($package,['=','<>','!=','<','>','<=','>=','in']);
        $connection = $package->connection;
        if ($package->relation == 'in') {
            $this->handleInStatement($connection, $package->key, $package->value);            
        } else {
            $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
        }
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($property)
    {
        
    }
    
    public function handlePropertyObject($property)
    {
        
    }
    
    public function handlePropertyTags($property)
    {
        
    }
    
    public function handlePropertyText($property)
    {
        
    }
    
    public function handlePropertyTime($property)
    {
        
    }

    public function handlePropertyVarchar($package)
    {
        $this->checkRelation($package,['=','<>','!=','<','>','<=','>=','in','begins with','contains','ends with','unary']);
        $connection = $package->connection;
        switch ($package->relation) {
            case 'in':
                $this->handleInStatement($connection, $package->key, $package->value);
                break;
            case 'begins with':
                $this->query->$connection($package->key, 'like', $package->value.'%');
                break;
            case 'ends with':
                $this->query->$connection($package->key, 'like', '%'.$package->value);
                break;
            case 'contains':
                $this->query->$connection($package->key, 'like', '%'.$package->value.'%');
                break;
            default:
                $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
        }
    }
    
}