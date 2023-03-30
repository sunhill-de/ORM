<?php

/**
 * @file QueryWhereCalculated.php
 * Provides the QueryWhereCalculated class
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Search;

use Sunhill\ORM\Properties\Property;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Search\QueryException;

/**
 * An abstract base class for where parts in a query. It provides a check for allowed relations and stores the required field for the query
 * @author klaus
 *
 */
class QueryWhereCalculated extends QueryWhere 
{
    
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

     protected function getInnerQuery()
     {
         $result = "select object_id from caching where fieldname = '".$this->field."' and value "; 
         switch ($this->relation) {
             case 'begins with':
                 $result .= 'like '.$this->escape($this->value.'%'); break;
             case 'ends with':
                 $result .= 'like '.$this->escape('%'.$this->value); break;
             case 'contains':
                 $result .= 'like '.$this->escape("%".$this->value."%"); break;
             case '!=':
             case '<>':    
                 $result .= '<> '.$this->escape($this->value); break;
             case 'in':
                 $result .= $this->assembleIn(); break;
             default:
                 $result .= $this->relation.' '.$this->escape($this->value); break;
         }
         return $result;
     }
     
     public function getThisWherePart() 
     {
         return $this->getQueryPrefix()." a.id in (".$this->getInnerQuery().')';
     }
    
}