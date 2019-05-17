<?php

namespace Sunhill;

use Illuminate\Support\Facades\DB;

class QueryException extends \Exception {}

class query_builder {

    protected $calling_class;
    
    protected $limit = '';
    
    protected $where = array();
    
    protected $searchfor = 'a.id';
    
    protected $used_tables = array();
    
    protected $next_table = 'b';
    
    public function __construct() {
        
    }
    
    public function where($field,$relation,$value) {
        $property = ($this->calling_class)::get_property_info($field);
        if (!$property->get_searchable()) {
            throw new QueryException("Nach dem Feld '$field' kann nicht gesucht werden.");
        }
        if ($property->has_feature('simple')) {
            $this->add_simple_where($property,$relation,$value);
        } else if ($property->has_feature('calculated')) {
            $this->add_calc_where($property,$relation,$value);
        }
        return $this;
    }
    
    private function add_simple_where($property,$relation,$value,$connect='and') {
        $result = array('connect'=>$connect,
                        'table'=>$this->get_table($property),
                        'where'=>$property->get_where($relation,$value));
        if (isset($this->where['simple'])) {
            $this->where['simple'][] = $result;
        } else {
            $this->where['simple'] = [$result];
        }
    }
    
    private function add_calc_where($property,$relation,$value,$connect='and') {
        $result = array('connect'=>$connect,
            'table'=>$this->get_calc_table(),
            'where'=>$property->get_where($relation,$value));
        if (isset($this->where['calc'])) {
            $this->where['calc'][] = $result;
        } else {
            $this->where['calc'] = [$result];
        }
    }
    
    private function get_calc_table() {
        if (!isset($this->used_tables['calc'])) {
            $this->used_tables['caching'] = $this->next_table++;
        }
        return $this->used_tables['caching'];
        
    }
    
    private function get_table($property) {
        if (!isset($this->used_tables[($property->get_class())::$table_name])) {
            $this->used_tables[($property->get_class())::$table_name] = $this->next_table++;
        }
        return $this->used_tables[($property->get_class())::$table_name];
    }
    
    public function limit($delta,$limit) {
        $this->limit = "$delta,$limit";    
    }
    
    public function get() {
        return $this->execute_query();
    }
    
    public function first() {
        $this->limit = '0,1';
        $result = $this->execute_query();
        return $result[0];
    }
    
    public function get_objects() {
        $query = $this->get();
        $result = array();
        foreach ($query as $id) {
            $result[] = \Sunhill\Objects\oo_object::load_object_of($id);
        }
        return $result;
    }
    
    public function first_object() {
        return \Sunhill\Objects\oo_object::load_object_of($this->first());
    }
    
    public function count() {
       $this->searchfor = 'count(*) as id';
       return $this->execute_query();
    }
    
    public function set_calling_class($calling_class) {
        $this->calling_class = $calling_class;
        $this->used_tables[$calling_class::$table_name] = 'a';
        return $this;
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
        if (isset($this->where['simple'])) {
            $result .= $this->get_simple_where();
            $first = false;
        }
        if (isset($this->where['calc'])) {
            $result .= $this->get_calc_where($first);
            $first = false;
        }
        return $result;
    }
    
    private function get_calc_where($first) {
        $result = '';
        foreach ($this->where['calc'] as $where) {
            if (!$first) {
                $result .= $where['connection'];
            } else {
                $first = false;
            }
            $result .= $where['table'].'.'.$where['where'];
        }
        return $result;
    }
    
    private function get_simple_where() {
        $result = '';
        $first = true;
        foreach ($this->where['simple'] as $where) {
            if (!$first) {
                $result .= $where['connection'];
            } else {
                $first = false;
            }
            $result .= $where['table'].'.'.$where['where'];
        }
        return $result;
    }
    
    private function get_used_tables() {
        $first = true;
        $result = '';
        foreach ($this->used_tables as $table => $letter) {            
            switch ($table) {
                case 'caching':
                    if (!$first) {
                        $result .= ' inner join caching as '.$letter.' on a.id = '.$letter.'.object_id';                        
                    } else {
                        $result .= 'caching as '.$letter;                        
                    }
                    break;
                default:
                    if (!$first) {
                        $result .= ' inner join '.$table.' as '.$letter.' on a.id = '.$letter.'.id';
                    } else {
                        $result .= $table.' as '.$letter;
                        $first = false;
                    }
            }
        }
        return $result;
    }
    
    private function postprocess_querystr() {
        
    }
}