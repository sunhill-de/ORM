<?php

namespace Sunhill\ORM\Search;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Utils\objectlist;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\SunhillException;

class QueryException extends SunhillException {}

class query_builder {

    protected $query_parts = [];
    
    protected $calling_class;
    
    protected $used_tables = array();
    
    protected $next_table = 'a';
    
    /**
     * Creates a new query object and passes the calling object class over
     */
    public function __construct(string $classname='') {
        if (!empty($classname)) {
            $this->set_calling_class($classname);
        }
    }
    
    /**
     * Since a search is initiazied by a specific class, the class is set here 
     * @param unknown $calling_class
     * @return \Sunhill\ORM\Search\query_builder
     */
    public function set_calling_class($calling_class) {
        $this->calling_class = $calling_class;
        return $this;
    }

    /**
     * Returns the calling class
     * @return unknown
     */
    public function get_calling_class() {
        return $this->calling_class;    
    }
    
    /**
     * Returns the part of the query identified by $part_id
     * @param string $part_id
     * @return string|unknown
     */
    protected function get_query_part(string $part_id) {
        return isset($this->query_parts[$part_id])?$this->query_parts[$part_id]->get_query_part():'';
    }
    
    /**
     * Sets a new query part identified by $part_id
     * @param string $part_id
     * @param query_atom $part
     */
    protected function set_query_part(string $part_id,query_atom $part,$connection=null) {
        if (!isset($this->query_parts[$part_id])) {
            // This part is not set yet, so just set it
            $this->query_parts[$part_id] = $part;
        } else {
            // This part is already set, so decide what to do
            if ($part->is_singleton()) {
                // replace a singleton
                $this->query_parts[$part_id] = $part;
            } else {
                $this->query_parts[$part_id]->link($part,$connection);
            }
        }
    }
    
    /**
     * Enters the passed table into the used_tables array and returns the given letter
     * @param string $table_name
     * @return string
     */
    public function get_table(string $table_name) {
        if (!isset($this->used_tables[$table_name])) {
            $this->used_tables[$table_name] = $this->next_table++;
        }         
        return $this->used_tables[$table_name];
    }
    
    protected function get_where_part($field,$relation,$value) {
        $property = ($this->calling_class)::get_property_object($field);
        if (is_null($property)) {
            throw new QueryException("The field '$field' is not found.");
        }
        if (! $property->get_searchable()) {
            throw new QueryException("The field '$field' is not searchable.");
        }
        switch ($property->type) {
            case 'tags':
                $part = new query_where_tag($this,$property,$relation,$value);
                break;
            case 'attribute_char':
            case 'attribute_float':
            case 'attribute_int':
            case 'attribute_float':
                $part = new query_where_attribute($this,$property,$relation,$value);
                break;
            case 'array_of_objects':
                $part = new query_where_array_of_objects($this,$property,$relation,$value);
                break;
            case 'array_of_strings':
                $part = new query_where_array_of_strings($this,$property,$relation,$value);
                break;
            case 'varchar':
                $part = new query_where_string($this,$property,$relation,$value);
                break;
            case 'object':
                $part = new query_where_object($this,$property,$relation,$value);
                break;
            case 'calculated':
                $part = new query_where_calculated($this,$property,$relation,$value);
                break;
            default:
                $part = new query_where_simple($this,$property,$relation,$value);
                break;
        }
        return $part;
    }
    
    public function where($field,$relation,$value=null) {
        $part = $this->get_where_part($field,$relation,$value);
        $this->set_query_part('where',$part,'and');
        return $this;
    }
    
    public function orWhere($field,$relation,$value=null) {
        $part = $this->get_where_part($field,$relation,$value);
        $this->set_query_part('where',$part,'or');
        return $this;
    }
    
    public function order_by($field,$asc=true) {    
        $this->set_query_part('order', new query_order($this,$field,$asc));
        return $this;
    }
    
    public function limit($delta,$limit) {
        $this->set_query_part('limit', new query_limit($this,$delta,$limit));
        return $this;
    }
    
// ****************** Query finalizations ***********************    
    /**
     * Returns the used tables for this query. All tables are joined as inner joins
     * @return string
     */
    protected function get_tables() {
        $first = true;
        $result = ' from ';
        foreach ($this->used_tables as $table_name => $alias) {
            if (!$first) {
                $result .= ' inner join ';
            } else {
                $master_alias = $alias;
            }
            $result .= $table_name.' as '.$alias;
            if (!$first) {
                $result .= " on $alias.id = $master_alias.id";
            }
            $first = false;
        }
        return $result;
    }
    
    /**
     * Assembles the queryparts togeteher and return the pure query-string
     * @return string
     */
    protected function finalize() {
        $query_str =
        $this->get_query_part('target').
        $this->get_tables().
        $this->get_query_part('where').
        $this->get_query_part('group').
        $this->get_query_part('order').
        $this->get_query_part('limit');
 
        return $query_str;
        
    }
    
    protected function prepare_query(bool $dump) {
        $query_str = $this->finalize();
        if ($dump) {
            return $query_str;
        } else {
            return $this->execute_query($query_str);
        }
    }
    
    /**
     * Returns all result of this query
     */
    public function get(bool $dump=false) {
        $this->set_query_part('target', new query_target_id($this));
        return $this->postprocess_results($this->prepare_query($dump));
    }
    
    /**
     * Returns the count of entries of this query
     * @param bool $dump
     * @return string|NULL|\Sunhill\ORM\Search\unknown|NULL[]
     */
    public function count(bool $dump=false) {
        $this->set_query_part('target', new query_target_count($this));
        $result = $this->prepare_query($dump);
        if ($dump) {
            return $result;
        } else {
            return $result[0]->count;
        }
    }
    
    /**
     * Returns the first entry of the query
     * @deprecated Should be replaces by ->first_id() or ->load()
     * @param bool $dump
     * @return \Sunhill\ORM\Search\unknown
     */
    public function first(bool $dump=false) {
        return $this->first_id($dump);
    }
    
    /**
     * Alias of ->first()
     * @param bool $dump
     * @return \Sunhill\ORM\Search\unknown
     */
    public function first_id(bool $dump=false) {
        $this->set_query_part('target', new query_target_id($this));
        $this->set_query_part('limit', new query_limit($this,0,1));
        $result = $this->prepare_query($dump);
        if ($dump) {
            return $result;
        } else {
            if (empty($result)) {
                return null;
            } else {
                return $result[0]->id;
            }
        }
    }
    
    /**
     * returns the loaded first entry of the query. If it doesn't exist it raises an exception
     * @throws QueryException
     * @return NULL
     */
    public function load() {
        $result = $this->load_if_exists();        
        if (empty($result)) {
            throw new QueryException("load() expects at least one result. Non returned");
        }
        return $result;
    }
    
    /**
     * return the laoded first entry of the query or null if it doesn't exist
     * @return unknown|NULL
     */
    public function load_if_exists() {
        $this->set_query_part('target', new query_target_id($this));
        $this->set_query_part('limit', new query_limit($this,0,1));
        $result = $this->prepare_query($dump);
        if (!empty($result)) {
            return oo_object::load_object_if($result[0]->id);       
        } else {
            return null;
        }
    }
    
    /**
     * Converts the database result into a objectlist 
     * @param unknown $result
     * @return unknown
     */
    protected function postprocess_results($result) {
        if (is_string($result)) {
            return $result; // We requested a dump
        } else {
            $return = new objectlist();
            foreach ($result as $entry) {
                $return[] = $entry->id;
            }
            return $return;
        }
    }
    
// ********************* Query-Management  ****************************
    protected function execute_query(string $querystr) {
        return DB::select(DB::raw($querystr));
    }
    
}