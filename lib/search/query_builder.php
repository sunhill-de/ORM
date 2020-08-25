<?php

namespace Sunhill\Search;

use Illuminate\Support\Facades\DB;

class QueryException extends \Exception {}

class query_builder {

    protected $query_parts = [];
    
    protected $calling_class;
    
    protected $limit = '';
    
    protected $where = array();
    
    protected $searchfor = 'a.id';
    
    protected $used_tables = array();
    
    protected $next_table = 'a';
    
    protected $order_by = '';
    
    protected $grouping = true;
    
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
     * @return \Sunhill\Search\query_builder
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
        return is_set($this->query_parts[$part_id])?$this->query_parts[$part_id]:'';
    }
    
    /**
     * Sets a new query part identified by $part_id
     * @param string $part_id
     * @param query_atom $part
     */
    protected function set_query_part(string $part_id,query_atom $part) {
        if (!is_set($this->query_parts[$part_id])) {
            // This part is not set yet, so just set it
            $this->query_parts[$part_id] = $part;
        } else {
            // This part is already set, so decide what to do
            if ($part->is_singleton()) {
                // replace a singleton
                $this->query_parts[$part_id] = $part;
            } else {
                
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
            $this->used_tables[$this->next_table] = $table_name;
        }         
        return $this->used_tables[$table_name];
    }
    
    public function where($field,$relation,$value) {
        $property = ($this->calling_class)::get_property_info($field);
        if (!$property->get_searchable()) {
            throw new QueryException("Nach dem Feld '$field' kann nicht gesucht werden.");
        }
        $this->add_where($property,$relation,$value);
        return $this;
    }

    private function add_where($property,$relation,$value,$connection='and') {
        $letter = $this->request_table($property,$relation,$value);
        $where = array('connect'=>$connection,'string'=>$property->get_where($relation,$value,$letter));
        $this->where[] = $where;
    }

    private function request_table($property,$relation,$value) {
        $table_name = $property->get_table_name($relation,$value);
        if (empty($table_name)) {
            return 'zz';
        }
        if (!isset($this->used_tables[$table_name])) {
            $letter= $this->next_table++;
            $table_join = $property->get_table_join($relation,$value,$letter);
            $this->used_tables[$table_name] = array('letter'=>$letter,'join'=>$table_join);
        }
        return $this->used_tables[$table_name]['letter'];
    }
    
    public function limit($delta,$limit) {
        $this->limit = "$delta,$limit";
        return $this;
    }
    
    public function order_by($field,$desc=false) {    
        $property = ($this->calling_class)::get_property_info($field);        
        $letter = $this->request_table($property,'=',0);
        $this->order_by = ' order by '.$letter.'.'.$field;
        if ($desc) {
            $this->order_by .= ' desc';
        }
        return $this;
    }
    
    protected function finalize() {
        $query_str =  
                $this->get_query_part('target').
                $this->get_tables();
        
    }
    
    public function get() {
        return $this->execute_query();
    }
    
    public function first() {
        $this->limit = '0,1';
        $result = $this->execute_query();
        if (is_array($result)) {
            return $result[0];
        } else {
            return $result;
        }
    }
    
    public function get_objects() {
        $query = $this->get();
        if (empty($query)) {
            return null;
        } else if (is_array($query)) {
            $result = array();
            foreach ($query as $id) {
                $result[] = \Sunhill\Objects\oo_object::load_object_of($id);
            }
            return $result;            
        } else {
            return $query;
        }
    }
    
    public function first_object() {
        return \Sunhill\Objects\oo_object::load_object_of($this->first());
    }
    
    public function count() {
       $this->searchfor = 'count(*) as id';
       $this->grouping = false;
       $this->order_by = '';
       return $this->execute_query();
    }
    
// ********************* Query-Management  ****************************
    protected function execute_query() {
        if (empty($this->where)) {
            $querystr = $this->get_all_querystr();
        } else {
            $querystr = $this->get_where_querystr();
        }
        $querystr .= $this->postprocess_querystr();
        return $this->postprocess_result(DB::select(DB::raw($querystr)));
    }
    
    /**
     * Bearbeitet die von DB::select kommenden Ergebnisse nach 
     * @param unknown $result
     * @return NULL|unknown|NULL[]
     */
    private function postprocess_result($result) {
        if (empty($result)) {
            return null;
        } else if (count($result) == 1) {
            return $result[0]->id;
        } else {
            $return = array();
            foreach ($result as $id) {
                $return[] = $id->id;
            }
            return $return;
        }        
    }
    
    private function get_all_querystr() {
        return 'select '.$this->searchfor.' from '.$this->calling_class::$table_name." as a";    
    }
    
    private function get_where_querystr() {
        $result = 'select '.$this->searchfor.' from '.$this->get_used_tables().' where ';
        $first = true;
        foreach ($this->where as $where) {
            if (!$first) {
                $result .= ' '.$where['connect'].' ';
            }
            $first = false;
            $result .= $where['string'];
        }
        return $result.($this->grouping?' group by a.id':'');
    }
    
    private function get_used_tables() {
        $result = $this->calling_class::$table_name." as a";
        foreach ($this->used_tables as $table => $info) {            
            if ($info['letter'] !== 'a') {
                if (is_string($info['join'])) {
                    $result .= ' inner join '.$table.' as '.$info['letter'].' '.$info['join'];                    
                } else {
                    $result .= $info['join']->get_special_join($info['letter']);
                }
            }
        }
        return $result;
    }
    
    private function postprocess_querystr() {
        if (!empty($this->order_by)) {
            $result =  $this->order_by;
        } else {
            $result = '';
   //         $result = ' order by a.id';
        }
        if (!empty($this->limit)) {
            $result .= ' limit '.$this->limit;
        }
        return $result;
    }
}