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
        $this->used_tables[$calling_class::$table_name] = array('letter'=>'a');
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
        foreach ($this->where as $where) {
            if (!$first) {
                $result .= $where['connect'];
            }
            $first = false;
            $result .= $where['string'];
        }
        return $result.' group by a.id';
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
        
    }
}