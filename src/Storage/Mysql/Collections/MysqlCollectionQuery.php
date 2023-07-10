<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Query\DBQuery;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Query\NotAllowedRelationException;
use Sunhill\ORM\Query\NoUnaryConditionException;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Query\WrongTypeException;

class MysqlCollectionQuery extends DBQuery implements HandlesProperties
{
    protected $collection;
    
    public function __construct($collection)
    {
        $this->collection = $collection;
        parent::__construct();
    }
    
    /**
     * Adds a condition to the query that filters all of the given classes that 
     * have associations (meaning any object or collection reference)
     * @return MysqlCollectionQuery
     */
    public function whereHasAssociations(): MysqlCollectionQuery
    {
        $this->handleWhere('where', 'has associations', null, null);
        return $this;    
    }
    
    /**
     * Adds a condition to the query that filters all of the given classes that don't
     * have associations (meaning any object or collection reference)
     * @return MysqlCollectionQuery
     */
    public function whereNotHasAssociations(): MysqlCollectionQuery
    {
        $this->handleWhere('whereNot', 'has associations', null, null);
        return $this;        
    }
    
    public function orWhereHasAssociations(): MysqlCollectionQuery
    {
        $this->handleWhere('orWhere', 'has associations', null, null);
        return $this;
    }
    
    public function orWhereNotHasAssociations(): MysqlCollectionQuery
    {
        $this->handleWhere('orWhereNot', 'has associations', null, null);
        return $this;
    }
    
    protected function getBasicTable()
    {
        return DB::table($this->collection::getInfo('table'));
    }
    
    protected function handleWhere(string $connection, $key, $relation, $value)
    {
        if (is_null($value)) {
            if (is_null($relation) || ($relation = '=')) {
                $relation = 'unary';   
            } else {
                $value = $relation;
                $relation = '=';
            }
        }
        $property = $this->collection->getProperty($key);
        if (empty($property)) {
            if (!$this->handleUnaryCondition($key)) {
                throw new UnknownFieldException("There is no field named '$key'");
            }
        }
        $package = new \StdClass();
        $package->property = $property;
        $package->connection = $connection;
        $package->key = $key;
        $package->relation = strtolower($relation);
        $package->value = $value;
        $this->mapProperty($property, $package);
    }
   
    protected function handleUnaryCondition($key): bool
    {
        $key = strtolower($key);        
        switch ($key) {
            case 'has associations':
                return true;
                break;
            case 'is associated':
                return true;
                break;
            case 'has attributes':
                return true;
                break;
        }
        return false;
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
            case '==':    
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
        $this->checkRelation($package, ['=','==','<>','!=','unary']);
        $connection = $package->connection;
        if ($package->relation == 'unary') {
            $this->query->$connection($package->key,'=',true);
        } else {
            $this->query->$connection($package->key,$package->relation,$package->value);
        }        
    }
    
    public function handlePropertyCalculated($property)
    {
        
    }
    
    public function handlePropertyCollection($property)
    {
        
    }

    public function handlePropertyDate($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','<','>','<=','>=','in','month','day','year']);
        $connection = $package->connection;
        switch ($package->relation) {
            case 'month':
                $connection .= 'Month';
                $this->query->$connection($package->key, $package->value);
                break;
            case 'day':
                $connection .= 'Day';
                $this->query->$connection($package->key, $package->value);
                break;
            case 'year':
                $connection .= 'Year';
                $this->query->$connection($package->key, $package->value);
                break;
            default:
                $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
                break;
        }        
    }
    
    public function handlePropertyDateTime($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','<','>','<=','>=','in','date','month','day','year','time']);
        $connection = $package->connection;
        switch ($package->relation) {
            case 'date':
                $connection .= 'Date';
                $this->query->$connection($package->key, $package->value);
                break;
            case 'time':
                $connection .= 'Time';
                $this->query->$connection($package->key, $package->value);
                break;
            case 'month':
                $connection .= 'Month';
                $this->query->$connection($package->key, $package->value);
                break;
            case 'day':
                $connection .= 'Day';
                $this->query->$connection($package->key, $package->value);
                break;
            case 'year':
                $connection .= 'Year';
                $this->query->$connection($package->key, $package->value);
                break;
            default:
                $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
                break;
        }        
    }
    
    public function handlePropertyEnum($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','<','>','<=','>=','in']);
        $connection = $package->connection;
        if ($package->relation == 'in') {
            $this->handleInStatement($connection, $package->key, $package->value);
        } else {
            $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
        }        
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyFloat($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','<','>','<=','>=','in']);
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
    
    public function handlePropertyObject($package)
    {
        $this->checkRelation($package,['=','<>','!=','in','unary']);
        $connection = $package->connection;
        if ($package->relation == 'unary') {
            $this->handleNullCondition($connection, $package->key);
            return;
        }
        if (is_a($package->value, ORMObject::class)) {
            $package->value = $package->value->getID();
            $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
            return;
        } 
        
        if ($package->relation == 'in') {
            for($i=0;$i<count($package->value);$i++) {
                if (is_a($package->value[$i], ORMObject::class)) {
                    $package->value[$i] = $package->value[$i]->getID();
                }
            }
            $this->handleInStatement($connection, $package->key, $package->value);
            return;
        }
        if (is_numeric($package->value)) {
            $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
            return;
        }
        throw new WrongTypeException("The given parameter can not be solved to an object.");
    }
    
    public function handlePropertyTags($property)
    {
        
    }
    
    public function handlePropertyText($package)
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
    
    public function handlePropertyTime($package)
    {
        $this->checkRelation($package,['=','==','!=','<>','<','>','>=','<=','in']);
        $connection = $package->connection;
        switch ($package->relation) {
            default:
                $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
                break;
        }
    }

    public function handlePropertyVarchar($package)
    {
        $this->checkRelation($package,['=','<>','!=','<','>','<=','>=','in','contains','unary']);
        $connection = $package->connection;
        switch ($package->relation) {
            case 'in':
                $this->handleInStatement($connection, $package->key, $package->value);
                break;
            case 'contains':
                $this->query->$connection($package->key, 'like', '%'.$package->value.'%');
                break;
            default:
                $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
        }
    }
    
}