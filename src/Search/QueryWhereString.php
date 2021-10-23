<?php

/**
 * @file QueryWhereString.php
 * Provides the QueryWhereString class
 * Lang en
 * Reviewstatus: 2021-10-21
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
class QueryWhereString extends QueryWhere 
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
    
     public function getThisWherePart() 
     {
         $result = $this->getQueryPrefix()." ";
         switch ($this->relation) {
             case 'begins with':
                 $this->value .= '%';
                 return $result.$this->field." like ".$this->escape($this->value);
                 break;
             case 'ends with':
                 $this->value = '%'.$this->value;
                 return $result.$this->field." like ".$this->escape($this->value);
                 break;
             case 'consists':
             case 'contains':
                 $this->value = '%'.$this->value.'%';
                 return $result.$this->field." like ".$this->escape($this->value);
                 break;
             default:
                 return parent::getThisWherePart();
         }
        return $result;
    }
    
}
