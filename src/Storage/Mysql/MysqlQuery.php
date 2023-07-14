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

class MysqlQuery extends DBQuery implements HandlesProperties
{
    use PropertyHelpers;
    
    protected $tranlate_relation = [
        '=='=>'=',
        '!='=>'<>',
        'has'=>'contains'
    ];
    
    protected $relations = [
        '='=>['arguments'=>'binary','type'=>['scalar','array','map']],
        '<>'=>['arguments'=>'binary','type'=>['scalar','array','map']],
        '<'=>['arguments'=>'binary','type'=>['scalar']],
        '>'=>['arguments'=>'binary','type'=>['scalar','array','map']],
        '<='=>['arguments'=>'binary','type'=>['scalar','array','map']],
        '>='=>['arguments'=>'binary','type'=>['scalar','array','map']],
        'contains'=>['arguments'=>'binary','type'=>['string','array','map','tags']],
        'begins with'=>['arguments'=>'binary','type'=>['string']],
        'ends with'=>['arguments'=>'binary','type'=>['string']],
        'all of'=>['arguments'=>'binary','type'=>['array','map','tag']],
        'any of'=>['arguments'=>'binary','type'=>['array','map','tag']],
        'none of'=>['arguments'=>'binary','type'=>['array','map','tag']],
        'all keys of'=>['arguments'=>'binary','type'=>['array','map','tag']],
        'any key of'=>['arguments'=>'binary','type'=>['map']],
        'none key of'=>['arguments'=>'binary','type'=>['map']],
        'has associations'=>['arguments'=>'none'],
        'is associated'=>['arguments'=>'none'],
        'has attributes'=>['arguments'=>'none'],
        'has tags'=>['arguments'=>'none']
    ];
    
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
        $list = $this->collectClasses();
        $first = array_pop($list)::getInfo('table');
        $query = DB::table($first);
        foreach ($list as $table) {
            $query->join($table::getInfo('table'),$table::getInfo('table').'.id','=',$first.'.id');
        }
        return $query;
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
                $letter = 'a';
                $subquery2 = DB::table($table)->select('id')->whereNotIn('value',$package->value);
                $subquery1 = DB::table($table.' as '.$letter++)->select('a.id')->where('a.value',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery1->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.value',$value);
                }
                $this->query->$connection('id',$subquery1->whereNotIn('id',$subquery2));
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
                $subquery = DB::table($table)->select('id');
                $this->invertCondition($package->connection, $subquery);
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
                $letter = 'a';
                $subquery = DB::table($table.' as '.$letter++)->select('a.id')->where('a.value',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.value',$value);
                }
                $this->query->$connection('id',
                    $subquery
                    );
                break;
            case 'none of':
                $subquery = DB::table($table)->select('id')->where('value',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery->orWhere('value',$value);
                }
                $this->invertCondition($package->connection, $subquery);
                break;
            case 'any key of':
                $subquery = DB::table($table)->select('id')->where('index',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery->orWhere('index',$value);
                }
                $this->query->$connection('id',
                    $subquery
                    );
                break;
            case 'all keys of':
                $letter = 'a';
                $subquery = DB::table($table.' as '.$letter++)->select('a.id')->where('a.index',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.index',$value);
                }
                $this->query->$connection('id',
                    $subquery
                    );
                break;
            case 'none key of':
                $subquery = DB::table($table)->select('id')->where('index',array_pop($package->value));
                foreach ($package->value as $value) {
                    $subquery->orWhere('index',$value);
                }
                $this->invertCondition($package->connection, $subquery);
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
    
    protected function handleCharacterField($package)
    {
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
    
    public function handleAttribute($package)
    {
        
    }
    
    public function handlePropertyArray($package)
    {
        $this->handleMapOrArray($package);
    }
    
    public function handlePropertyBoolean($package)
    {
        $this->checkRelation($package, ['=','==','<>','!=','unary']);
        $connection = $package->connection;
        if (($package->relation == 'unary') ) {
            $this->query->$connection($package->key,'=',true);
        } else {
            $this->query->$connection($package->key,$package->relation,$package->value);
        }        
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
          //      $query->$connection('cont');
                break;
            case 'any of':
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