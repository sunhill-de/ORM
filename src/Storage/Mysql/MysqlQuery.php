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
use Sunhill\ORM\Facades\Objects;

class MysqlQuery extends DBQuery 
{
 
    use PropertyHelpers;
    
    protected $collection;
    
    protected $maintable;
    
    public function __construct($collection)
    {
        $this->collection = $collection;
        parent::__construct();
    }
 
    // Public methods
    public function getObjects(): \Illuminate\Support\Collection
    {
        $this->target = 'getObjects';
        return $this->execute();        
    }
    
    protected function handleOrder()
    {
        if (is_a($this->collection,ORMObject::class) && ($this->order_key == 'id')) {
            $this->order_key = 'objects.id';
        }
        parent::handleOrder();    
    }
    
    protected function returnObjects()
    {
        $result = $this->finalizeQuery()->get();
        $newresult = [];
        foreach ($result as $object_desc) {
            $object = Objects::load($object_desc->id);
            $newresult[] = $object;
        }
        return collect($newresult);
    }
    
    protected function execute()
    {
        if ($this->target == 'getObjects') {
            return $this->returnObjects();
        } else {
            return parent::execute();
        }
    }
    
    // Implemented abstract methods
    protected function getBasicTable()
    {
        $list = $this->collectClasses();
        $first = array_pop($list)::getInfo('table');
        $this->maintable = $first;
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
    
    protected function handleClosure($connection, $key)
    {
        $this->query->$connection(function($query) use ($key) {
            $subquery = new MysqlQuery($this->collection);
            $subquery->appendToSubquery($query, $key);            
        });
    }
    
    protected function handleWhere(string $connection, $key, $relation, $value)
    {
        if ($key instanceof \Closure) {
            $this->handleClosure($connection, $key);
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
    
    protected function getSubtableName($key)
    {
        return $this->collection::getPropertyObject($key)->owner::getInfo('table').'_'.$key;
    }
    
    protected function handleWhereArrayEquals(string $connection, $key, $value)
    {
        $connection .= 'In';
        $table = $this->getSubtableName($key);
        
        $letter = 'a';
        $subquery2 = DB::table($table.' as zz')->select('zz.id')->whereNotIn('zz.value',$value);
        $subquery1 = DB::table($table.' as '.$letter++)->select('a.id')->where('a.value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery1->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.value',$singlevalue);
        }
        $this->query->$connection($this->maintable.'.id',$subquery1->whereNotIn('a.id',$subquery2));        
    }
    
    protected function handleWhereAllOf(string $connection, $key, $value)
    {
        $connection .= 'In';
        $table = $this->getSubtableName($key);
        
        $letter = 'a';
        $subquery = DB::table($table.' as '.$letter++)->select('a.id')->where('a.value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.value',$singlevalue);
        }
        $this->query->$connection($this->maintable.'.id',
            $subquery
            );        
    }
    
    protected function handleWhereAnyOf(string $connection, $key, $value)
    {
        $connection .= 'In';
        $table = $this->getSubtableName($key);
        
        $subquery = DB::table($table)->select('id')->where('value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->orWhere('value',$singlevalue);
        }
        $this->query->$connection($this->maintable.'.id', $subquery );        
    }
    
    protected function invertCondition($connection, $subquery)
    {
        switch ($connection) {
            case 'where':
                $this->query->whereNotIn($this->maintable.'.id',$subquery);
                break;
            case 'whereNot':
                $this->query->whereIn($this->maintable.'.id',$subquery);
                break;
            case 'orWhere':
                $this->query->orWhereNotIn($this->maintable.'.id',$subquery);
                break;
            case 'orWhereNot':
                $this->query->orWhereIn($this->maintable.'.id',$subquery);
        }
    }
    
    protected function handleWhereNoneOf(string $connection, $key, $value)
    {
         $table = $this->getSubtableName($key);
        
        $subquery = DB::table($table)->select('id')->where('value',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->orWhere('value', $singlevalue);
        }
        $this->invertCondition($connection, $subquery);       
    }
    
    protected function handleWhereEmpty(string $connection, $key)
    {
        $table = $this->getSubtableName($key);
        
        $subquery = DB::table($table)->select('id');
        $this->invertCondition($connection, $subquery);        
    }
    
    protected function handleWhereAnyKeyOf(string $connection, $key, $value)
    {
        $table = $this->getSubtableName($key);
        $connection .= 'In';
        
        $subquery = DB::table($table)->select('id')->where('index',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->orWhere('index',$singlevalue);
        }
        $this->query->$connection($this->maintable.'.id', $subquery );
    }
    
    protected function handleWhereAllKeysOf(string $connection, $key, $value)
    {
        $table = $this->getSubtableName($key);
        $connection .= 'In';
        
        $letter = 'a';
        $subquery = DB::table($table.' as '.$letter++)->select('a.id')->where('a.index',array_pop($value));
        foreach ($value as $singlevalue) {
            $subquery->join($table.' as '.$letter,$letter.'.id','=','a.id')->where($letter++.'.index',$singlevalue);
        }
        $this->query->$connection($this->maintable.'.id', $subquery );
    }
    
    protected function handleWhereNoneKeyOf(string $connection, $key, $value)
    {
        $table = $this->getSubtableName($key);
        
        $subquery = DB::table($table)->select('id')->where('index',array_pop($value));
        foreach ($value as $value) {
            $subquery->orWhere('index',$value);
        }
        $this->invertCondition($connection, $subquery);
    }

    protected function handleWhereTagAllOf(string $connection, $key, $value)
    {
        $subquery = DB::table('tagobjectassigns as a')->select('container_id');
        if (!is_array($value)) {
            $value = [$value];
        }
        $letter = 'b';
        foreach ($value as $tag) {
            if (is_string($tag)) {
                $subquery->whereExists(function ($query) use ($letter, $tag) {
                    $first = $letter++;
                    $second = $letter++;
                    $query->select(DB::raw(1))->from('tagobjectassigns as '.$first)
                    ->join('tagcache as '.$second,$first.'.tag_id','=',$second.'.tag_id')
                    ->whereColumn('a.container_id',$first.'.container_id')
                    ->where($second.'.path_name',$tag);
                });
                    
            } else if (is_int($tag)){
                $subquery->whereExists(function ($query) use ($letter, $tag){
                    $query->select(DB::raw(1))->from('tagobjectassigns as '.$letter)
                          ->whereColumn('a.container_id',$letter.'.container_id')
                          ->where($letter++.'.tag_id',$tag);                                
                });
            } else if (is_a($tag, Tag::class)) {
                $subquery->whereExists(function ($query) use ($letter, $tag){
                    $query->select(DB::raw(1))->from('tagobjectassigns as '.$letter)
                    ->whereColumn('a.container_id',$letter.'.container_id')
                    ->where($letter++.'.tag_id',$tag->getID());
                });                    
            }
        }
        $connection .= 'In';
        $this->query->$connection($this->maintable.'.id',$subquery);
    }
    
    protected function handleWhereTagAnyOf(string $connection, $key, $value)
    {
        $ids = [];
        $names = [];
        if (!is_array($value)) {
            $value = [$value];
        }
        foreach ($value as $tag) {
            if (is_string($tag)) {
                $names[] = $tag;
            } else if (is_int($tag)) {
                $ids[] = $tag;
            } else if (is_a($tag, Tag::class)) {
                $ids[] = $tag->getID();
            }
        }
        if (!empty($ids) && !empty($names)) {
            $subquery = DB::table('tagobjectassigns as a')
            ->join('tagcache as b','a.tag_id','=','b.tag_id')
            ->select('a.container_id')
            ->whereIn('a.tag_id',$value)
            ->orWhereIn('b.path_name',$names);
            
        } else if (empty($names)) {
            $subquery = DB::table('tagobjectassigns')
                        ->select('container_id')
                        ->whereIn('tag_id',$value);
        } else {
            $subquery = DB::table('tagobjectassigns as a')
                        ->join('tagcache as b','a.tag_id','=','b.tag_id')
                        ->select('a.container_id')
                        ->whereIn('b.path_name',$names);
        }
        $connection .= 'In';
        $this->query->$connection($this->maintable.'.id',$subquery);
    }
    
    protected function handleWhereTagNoneOf(string $connection, $key, $value)
    {
        $ids = [];
        $names = [];
        if (!is_array($value)) {
            $value = [$value];
        }
        foreach ($value as $tag) {
            if (is_string($tag)) {
                $names[] = $tag;
            } else if (is_int($tag)) {
                $ids[] = $tag;
            } else if (is_a($tag, Tag::class)) {
                $ids[] = $tag->getID();
            }
        }
        if (!empty($ids) && !empty($names)) {
            $subquery = DB::table('tagobjectassigns as a')
            ->join('tagcache as b','a.tag_id','=','b.tag_id')
            ->select('a.container_id')
            ->whereIn('a.tag_id',$value)
            ->orWhereIn('b.path_name',$names);            
        } else if (empty($names)) {
            $subquery = DB::table('tagobjectassigns')
            ->select('container_id')
            ->whereIn('tag_id',$value);
        } else {
            $subquery = DB::table('tagobjectassigns as a')
            ->join('tagcache as b','a.tag_id','=','b.tag_id')
            ->select('a.container_id')
            ->whereIn('b.path_name',$names);
        }
        
        $connection .= 'In';
        switch ($connection) {
            case 'whereIn':
                $this->query->whereNotIn($this->maintable.'.id',$subquery);
                break;
            case 'whereNotIn':
                $this->query->whereIn($this->maintable.'.id',$subquery);
                break;
            case 'orWhereIn':
                $this->query->orWhereNotIn($this->maintable.'.id',$subquery);
                break;
            case 'orWhereNotIn':
                $this->query->orWhereIn($this->maintable.'.id',$subquery);
                break;
        }
    }
    
    protected function handleWhereHasAssociations(string $connection)
    {
        $connection .= 'In';
        $this->query->$connection($this->maintable.'.id',DB::table('objectobjectassigns')->select('container_id')->groupBy('container_id'));
    }
    
    protected function handleWhereIsAssociated(string $connection)
    {
        $connection .= 'In';
        $this->query->$connection($this->maintable.'.id',DB::table('objectobjectassigns')->select('target_id')->groupBy('target_id'));
    }
    
    protected function handleWhereHasTags(string $connection)
    {
        $connection .= 'In';
        $this->query->$connection($this->maintable.'.id',DB::table('tagobjectassigns')->select('container_id')->groupBy('container_id'));
        
    }
    
    protected function handleWhereHasAttributes(string $connection)
    {
        $connection .= 'In';
        $this->query->$connection($this->maintable.'.id',DB::table('attributeobjectassigns')->select('object_id')->groupBy('object_id'));
        
    }
    
}