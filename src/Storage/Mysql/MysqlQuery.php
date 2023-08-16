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

class MysqlQuery extends DBQuery 
{
 
    use PropertyHelpers;
    
    protected $collection;
    
    public function __construct($collection)
    {
        $this->collection = $collection;
        parent::__construct();
    }
 
    // Public methods
    
    // Implemented abstract methods
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
    
    /**
     * Creates a WhereParser object, passes the given params to it and return the resulting array
     * @param string $connection
     * @param unknown $key
     * @param unknown $relation
     * @param unknown $value
     * @return array 
     */
    public function parseWhereStatement(string $connection, $key, $relation, $value): array
    {
        $parser = new WhereParser();
        $parser->setClass($this->collection::class)
               ->setConnection($connection)
               ->setKey($key)
               ->setRelation($relation)
               ->setValue($value);
        return $parser->parseWhere();        
    }
    
    protected function handleWhere(string $connection, $key, $relation, $value)
    {
        if ($key instanceof \Closure) {
            $this->query->$connection($key);
            return;
        }
        $params = $this->parseWhereStatement($connection, $key, $relation, $value);
        $method = array_shift($params);
        $this->$method(...$params);
    }
    
    /**
     * Handles simple where statements with simple fields
     * @param string $connection
     * @param unknown $key
     * @param unknown $relation
     * @param unknown $value
     */
    protected function handleWhereSimple(string $connection, $key, $relation, $value)
    {
        $this->query->$connection($key, $relation, $value);
    }

    /**
     * Special case for boolean they have to be reduced to true or false
     * @param string $connection
     * @param unknown $key
     * @param unknown $relation
     * @param unknown $value
     */
    protected function handleWhereBoolean(string $connection, $key, $relation, $value)
    {
        if (!is_null($value)) {
            $value = ($value == false)?false:true;
        } 
        $this->query->$connection($key, $relation, $value);
    }

    /**
     * Translates to ->whereIn() and its buddies
     * @param string $connection
     * @param unknown $key
     * @param unknown $value
     */
    protected function handleWhereIn(string $connection, $key, $value)
    {
        $connection .= 'In';
        $this->query->$connection($key, $value);
    }
    
    /**
     * Handles strings with wildcards
     * @param string $connection
     * @param unknown $key
     * @param unknown $value
     */
    protected function handleWhereLike(string $connection, $key, $value)
    {
        $this->query->$connection($key, 'like', $value);
    }
    
    /**
     * Handles special date/time functions
     * @param string $connection
     * @param unknown $key
     * @param unknown $relation
     * @param unknown $value
     */
    protected function handleWhereDateTime(string $connection, $key, $relation, $value)
    {
        $connection .= ucfirst($relation);
        $this->query->$connection($key, $value);
    }
    
    protected function handleWhereArrayEquals(string $connection, $key, $value)
    {
        $connection .= 'In';
        $table = $this->collection::getInfo('table').'_'.$key;
        
        $letter = 'a';
        $subquery2 = DB::table($table.' as zz')->select('zz.id')->whereNotIn('zz.value',$value);
        $subquery1 = DB::table($table.' as '.$letter++)->select('a.id')->where('a.value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery1->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.value',$singlevalue);
        }
        $this->query->$connection('id',$subquery1->whereNotIn('a.id',$subquery2));        
    }
    
    protected function handleWhereAllOf(string $connection, $key, $value)
    {
        $connection .= 'In';
        $table = $this->collection::getInfo('table').'_'.$key;
        
        $letter = 'a';
        $subquery = DB::table($table.' as '.$letter++)->select('a.id')->where('a.value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.value',$singlevalue);
        }
        $this->query->$connection('id',
            $subquery
            );        
    }
    
    protected function handleWhereAnyOf(string $connection, $key, $value)
    {
        $connection .= 'In';
        $table = $this->collection::getInfo('table').'_'.$key;
        
        $subquery = DB::table($table)->select('id')->where('value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->orWhere('value',$singlevalue);
        }
        $this->query->$connection( 'id', $subquery );        
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
    
    protected function handleWhereNoneOf(string $connection, $key, $value)
    {
         $table = $this->collection::getInfo('table').'_'.$key;
        
        $subquery = DB::table($table)->select('id')->where('value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->orWhere('value', $singlevalue);
        }
        $this->invertCondition($connection, $subquery);       
    }
    
    protected function handleWhereEmpty(string $connection, $key)
    {
        $table = $this->collection::getInfo('table').'_'.$key;
        
        $subquery = DB::table($table)->select('id');
        $this->invertCondition($connection, $subquery);        
    }
    
    protected function handleWhereAnyKeyOf(string $connection, $key, $value)
    {
        $table = $this->collection::getInfo('table').'_'.$key;
        $connection .= 'In';
        
        $subquery = DB::table($table)->select('id')->where('index',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->orWhere('index',$singlevalue);
        }
        $this->query->$connection( 'id', $subquery );
    }
    
    protected function handleWhereAllKeysOf(string $connection, $key, $value)
    {
        $table = $this->collection::getInfo('table').'_'.$key;
        $connection .= 'In';
        
        $letter = 'a';
        $subquery = DB::table($table.' as '.$letter++)->select('a.id')->where('a.index',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.index',$singlevalue);
        }
        $this->query->$connection( 'id', $subquery );
    }
    
    protected function handleWhereNoneKeyOf(string $connection, $key, $value)
    {
        $table = $this->collection::getInfo('table').'_'.$key;
        
        $subquery = DB::table($table)->select('id')->where('index',array_pop($value));
        foreach ($value as $value) {
            $subquery->orWhere('index',$value);
        }
        $this->invertCondition($connection, $subquery);
    }
}