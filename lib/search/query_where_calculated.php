<?php

namespace Sunhill\Search;

use Sunhill\Properties\oo_property;
use Illuminate\Support\Facades\DB;
use Sunhill\Search\QueryException;

/**
 * An abstract base class for where parts in a query. It provides a check for allowed relations and stores the required field for the query
 * @author klaus
 *
 */
class query_where_calculated extends query_where {
    
    protected $allowed_relations = 
        [
            '='=>'scalar',
            '<'=>'scalar',
            '>'=>'scalar',
            '<='=>'scalar',
            '>='=>'scalar',
            '<>'=>'scalar',
            '!='=>'scalar',
            'in'=>'array',
            'begins with'=>'scalar',
            'ends with'=>'scalar',
            'consists'=>'scalar',
            'contains'=>'scalar',
        ];
    
     public function get_this_where_part() {
         $result = $this->get_query_prefix()." ";
         switch ($this->relation) {
             case '=':
                  return $result.'a.id in (select object_id from caching where value = '.$this->escape($this->value).')';
                  break;
             case '<>':
             case '!=':
                 return $result.'a.id not in (select object_id from caching where value = '.$this->escape($this->value).')';
                 break;
             case 'begins with':
                 $this->value .= '%';
                 return $result.'a.id in (select object_id from caching where value like '.$this->escape($this->value).')';
                 break;
             case 'ends with':
                 $this->value = '%'.$this->value;
                 return $result.'a.id in (select object_id from caching where value like '.$this->escape($this->value).')';
                 break;
             case 'consists':
             case 'contains':
                 $this->value = '%'.$this->value.'%';
                 return $result.'a.id in (select object_id from caching where value like '.$this->escape($this->value).')';
                 break;
             default:
                 return parent::get_this_where_part();
         }
        return $result;
    }
    
}