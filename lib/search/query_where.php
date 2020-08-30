<?php

namespace Sunhill\Search;

use Illuminate\Database\QueryException;
use Sunhill\Properties\oo_property;
use Illuminate\Support\Facades\DB;

/**
 * An abstract base class for where parts in a query. It provides a check for allowed relations and stores the required field for the query
 * @author klaus
 *
 */
abstract class query_where extends query_atom {
    
    protected $field;
    
    protected $relation;
    
    protected $value;
    
    protected $allowed_relations = ['=','<','>','<=','>=','<>','!='];
    
    protected $alias;
    
    public function __construct(query_builder $parent_query,oo_property $field,string $relation,$value) {
        parent::__construct($parent_query);
        if (!$this->is_allowed_relation($relation,$value)) {
            throw new QueryException("'$relation' is not an allowed relation in this context.");
        }
        $this->alias = $parent_query->get_table($this->get_table_name($field));    
        $this->field = $field->get_name();
        $this->relation = $relation;
        $this->value = $value;
    }
    
    protected function get_table_name(oo_property $field) {
        $owner = $field->get_class();
        return $owner::$table_name;
    }
    
    /**
     * Checks, if this relation is allowed in this context
     * @param string $relation
     * @return boolean true, if its an allowed relation, otherwise false
     */
    protected function is_allowed_relation(string $relation,$value) {
        switch ($relation) {
            case '=':
            case '<':
            case '>':
            case '<=':
            case '>=':
            case '<>':
            case '!=':
                return is_scalar($value);
                break;
            case 'in':
                return is_array($value);
                break;
            default:
                return false;
        }
    }
    
    protected function get_query_prefix() {
        if (is_null($this->prev)) {
            return ' where';
        } else {
            return '';
        }
    }
    
    public function get_query_part() {
        if ($this->relation == 'in') {
            $result = $this->get_query_prefix().' '.$this->alias.'.'.$this->field.' in (';
            $first = true;
            foreach ($this->value as $value) {
                $result .= ($first?'':',').$this->escape($value);
                $first = false;
            }
            $result .= ')';
        } else {
            $result = $this->get_query_prefix().' '.$this->alias.'.'.$this->field.' '.$this->relation." ".$this->escape($this->value);
        }
        if (isset($this->next)) {
            $result .= ' '.$this->connection.=$this->next->get_query_part();
        }
        return $result;
    }
    
    protected function escape(string $sample) {
        return DB::connection()->getPdo()->quote($sample);
    }
}