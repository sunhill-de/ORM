<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Query\DBQuery;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Query\NotAllowedRelationException;
use Sunhill\ORM\Query\NoUnaryConditionException;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Query\WrongTypeException;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Query\TooManyWhereParametersException;

class MysqlQuery extends DBQuery implements HandlesProperties
{
    use PropertyHelpers;
    
    
    /**
     * Adds a condition to the query that filters all of the given classes that
     * have associations (meaning any object or collection reference)
     * @return MysqlCollectionQuery
     */
    public function whereHasAssociations(): MysqlCollectionQuery
    {
        $this->handleUnaryCondition('where', 'has associations');
        return $this;
    }
    
    /**
     * Adds a condition to the query that filters all of the given classes that don't
     * have associations (meaning any object or collection reference)
     * @return MysqlCollectionQuery
     */
    public function whereNotHasAssociations(): MysqlCollectionQuery
    {
        $this->handleUnaryCondition('whereNot', 'has associations');
        return $this;
    }
    
    public function orWhereHasAssociations(): MysqlCollectionQuery
    {
        $this->handleUnaryCondition('orWhere', 'has associations');
        return $this;
    }
    
    public function orWhereNotHasAssociations(): MysqlCollectionQuery
    {
        $this->handleUnaryCondition('orNotwhere', 'has associations');
        return $this;
    }
    
    protected function handleUnaryCondition(string $connection, string $condition)
    {
        switch ($condition) {
            case 'has associations':
                break;
            case 'is associated':
                break;
            case 'has tags':
                break;
            case 'has attributes':
                break;
        }
    }
    
    protected function mapRelation($connection, $key, $relation, $value)
    {
        
    }
    
    protected function prepareValue($value, $relation_descriptor)
    {
        
    }
    
    protected function handleWhere(string $connection, $key, $relation, $value)
    {
        /*       if (isset($this->relations[$key]) && ($this->relations[$key]['arguments'])) {
         return $this->mapRelation($connection, null, $key, null);
         }
         if (isset($this->relations[$relation])) {
         return $this->mapRelation($connection, $key, $relation, $this->prepareValue($value, $this->relations[$key]));
         }
         */     $reserved_relations = ['empty','='];
        if (is_null($value)) {
            if (is_null($relation) || ($relation == '=')) {
                $relation = 'unary';
            } else if (!in_array($relation,$reserved_relations)) {
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
    
    protected function invertCondition($connection, $subquery)
    {
        switch ($connection) {
            case 'where':
                $this->query->whereNotIn('id',$subquery);
                break;
            case 'whereNot':
                $this->query->whereIn('id',$subquery);
                break;
            case 'orWhere':
                $this->query->orWhereNotIn('id',$subquery);
                break;
            case 'orWhereNot':
                $this->query->orWhereIn('id',$subquery);
        }
    }
    
    protected function handleMapOrArrayGeneral($package)
    {
        $connection = $package->connection.'In';
        $table = $this->collection::getInfo('table').'_'.$package->key;
        switch ($package->relation) {
            case '=':
            case '==':
                break;
            case '<>':
            case '!=':
                break;
            case 'contains':
                $this->query->$connection('id',
                DB::table($table)->select('id')->where('value', $package->value)
                );
                break;
            case 'empty':
            case 'unary':
                break;
            case 'any of':
                 break;
            case 'all of':
                break;
            case 'none of':
                break;
            case 'any key of':
                break;
            case 'all keys of':
                break;
            case 'none key of':
                break;
        }
    }
    
    protected function convertPropertiesCollectionValues($value)
    {
        if (is_a($value, PropertiesCollection::class)) {
            return $value->getID();
        } else if (is_int($value)) {
            return $value;
        } else if (is_array($value)) {
            $result = [];
            foreach ($value as $entry) {
                $result[] = $this->convertPropertiesCollectionValues($entry);
            }
            return $result;
        }
    }
    
    protected function handleMapOrArrayOfObjects($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','contains','empty','any of','all of','none of','any of class','all of class','none of class','unary','any key of','all keys of','none key of']);
        $package->value = $this->convertPropertiesCollectionValues($package->value);
        switch ($package->relation) {
            case 'any of class':
                break;
            case 'all of class':
                break;
            case 'none of class':
                break;
            default:
                $this->handleMapOrArrayCommon($package);
        }
    }
    
    protected function handleMapOrArrayOfCollections($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','contains','empty','any of','all of','none of','any key of','all keys of','none key of']);
        $package->value = $this->convertPropertiesCollectionValues($package->value);
        $this->handleMapOrArrayCommon($package);
    }
    
    protected function handleMapOrArrayOfOther($package)
    {
        $this->checkRelation($package,['=','==','<>','!=','contains','empty','any of','all of','none of','any key of','all keys of','none key of']);
        $this->handleMapOrArrayGeneral($package);
    }
    
    protected function handleMapOrArray($package)
    {
        $property = $this->collection->getProperty($package->key);
        if ($property->type == PropertyObject::class) {
            return $this->handleMapOrArrayOfObjects($package);
        }
        if ($property->type  == PropertyCollection::class) {
            return $this->handleMapOrArrayOfCollections($package);
        }
        $this->handleMapOrArrayOfOther($package);
    }
    
    public function handlePropertyCalculated($package)
    {
        $this->checkRelation($package,['=','<>','!=','<','>','<=','>=','in','begins with','contains','ends with','unary']);
        $this->handleCharacterField($package);
    }
    
    public function handlePropertyCollection($package)
    {
        $this->checkRelation($package,['=','<>','!=','in','unary']);
        $package->value = $this->convertPropertiesCollectionValues($package->value);
        
    }
    
    public function handlePropertyInformation($property){}
        
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($package)
    {
        $this->handleMapOrArray($package);
    }
    
    public function handlePropertyObject($package)
    {
        $this->checkRelation($package,['=','<>','!=','in','unary']);
        $package->value = $this->convertPropertiesCollectionValues($package->value);
        
        $connection = $package->connection;
        switch ($package->relation) {
            case 'unary':
                $this->handleNullCondition($connection, $package->key);
                break;
            case 'in':
                $this->handleInStatement($connection, $package->key, $package->value);
                break;
            default:
                $this->handleSimpleConditions($connection, $package->key, $package->relation, $package->value);
                break;
        }
    }
    
    protected function convertTags($package)
    {
        if (is_a($package->value,Tag::class)) {
            $package->value = $package->value->getID();
        } else if (is_array($package->value)) {
            $result = [];
            foreach ($package->value as $entry) {
                if (is_a($entry,Tag::class)) {
                    $result[] = $entry->getID();
                } else {
                    $result[] = $entry;
                }
            }
            return $result;
        } else {
            return $package->value;
        }
    }
    
    public function handlePropertyTags($package)
    {
        $this->checkRelation($package,['contains','has','any of','all of','none of','empty']);
        $package->value = $this->convertTags($package);
        
        $connection = $package->connection;
        switch ($package->relation) {
            case 'contains':
            case 'has':
                $connection .= 'In';
                $subquery = DB::table('tagobjectassigns')->select('container_id');
                if (is_string($package->value)) {
                    $subquery->join('tagcache','tagobjectassigns.tag_id','=','tagcache.tag_id')->where('path_name',$package->value);
                } else {
                    $subquery->where('tag_id',$package->value);
                }
                $this->query->$connection('objects.id',$subquery);
                break;
            case 'any of':
                $subquery = DB::table($table)->select('id')->where('value',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery->orWhere('value',$value);
                }
                $this->query->$connection('id',
                    $subquery
                    );
                break;
            case 'all of':
                break;
            case 'none of':
                break;
            case 'empty':
                break;
        }
    }
    
    public function handlePropertyText($package)
    {
        $this->checkRelation($package,['=','<>','!=','begins with','contains','ends with','unary']);
        $this->handleCharacterField($package);
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
        $this->checkRelation($package,['=','<>','!=','<','>','<=','>=','in','begins with','contains','ends with','unary']);
        $this->handleCharacterField($package);
    }
    
}