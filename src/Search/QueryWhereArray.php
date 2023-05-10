<?php
/**
 * @file QueryWhereArray.php
 * Provides the QueryWhereArray class
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

use Illuminate\Support\Facades\DB;

abstract class QueryWhereArray extends QueryWhere 
{
    
    protected $allowed_relations = 
        [
                'has'=>'scalar',
                'has not'=>'scalar',
                'one of'=>'array',
                'all of'=>'array',
                'none of'=>'array',
                'has any'=>'unary',
                'not empty'=>'unary',
                'has none'=>'unary',
                'empty'=>'unary'
        ];
    
        protected $help_alias;
        
        abstract protected function getAssocTable();
        abstract protected function getAssocField();
        abstract protected function getElementIDList($value);
        abstract protected function getElement($element);

        protected function getIDField()
        {
            return $this->help_alias.'.id';
        }
        
        protected function getFieldPart()
        {
            return "";    
        }
        
        public function getThisWherePart() 
        {
            $this->help_alias = $this->parent_query->getAliasOnly();
            
            $result = $this->getQueryPrefix();
            switch ($this->relation) {
                case 'has':
                case 'one of':
                    $result .= ' a.id in (select '.$this->getIDField().' from '.$this->getAssocTable().' where '.$this->getAssocField().' '.$this->getElementIDList($this->value).')';
                    break;
                case 'has not':
                case 'none of':
                    $result .= ' a.id not in (select '.$this->getIDField().' from '.$this->getAssocTable().' where '.$this->getAssocField().' '.$this->getElementIDList($this->value).')';
                    break;
                case 'has any':
                case 'not empty':
                    $result .= ' a.id in (select '.$this->getIDField().' from '.$this->getAssocTable().' where 1 '.$this->getFieldPart().')';
                    break;
                case 'has none':
                case 'empty':
                    $result .= ' a.id not in (select '.$this->getIDField().' from '.$this->getAssocTable().' where 1 '.$this->getFieldPart().')';
                    break;
                case 'all of':
                    $result .= ' ('.$this->getAllOfPart().')';
                    break;
            }
            return $result;
        }
        
        protected function getAllOfPart() 
        {
            $result = '';
            $first = true;
            foreach ($this->value as $value) {
                $value = $this->getElement($value);
                $result .= ($first?'':' and ').' exists (select '.$this->getIDField().' from '.$this->getAssocTable().' where '.$this->getIDField().' = a.id and '.$this->getAssocField().$value.' '.$this->getFieldPart().')';
                $first = false;
            }
            return $result;
        }
}