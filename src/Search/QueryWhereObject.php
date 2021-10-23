<?php

/**
 * @file QueryWhereObject.php
 * Provides the QueryWhereObject class
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

class QueryWhereObject extends QueryWhere 
{
    
    protected $allowed_relations = 
        [
            '='=>'object',
            '!='=>'object',
            '<>'=>'object',
            'in'=>'array',            
        ];
            
        protected function getValue() 
        {
            if (is_int($this->value)) {
                return $this->escape($this->value);
            } else {
                return $this->escape($this->value->getID());
            }
        }
        
        public function getThisWherePart() 
        {
            $result = $this->getQueryPrefix();
            switch ($this->relation) {
                case '=':
                    if (is_null($this->value)) {
                        $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."')";
                    } else {
                        $value = $this->getValue();
                        $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id = $value)";
                    }
                    break;
                case '<>':
                case '!=':
                    if (is_null($this->value)) {
                        $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."')";
                    } else {
                        $value = $this->getValue();
                        $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id = $value)";                        
                    }
                    break;
                case 'in':
                    $element_list = $this->getElementList();
                    $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id in ($element_list)";
                    break;
                case 'not in':
                    $element_list = $this->getElementList();
                    $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id in ($element_list)";
                    break;
            }
            return $result;
        }
        
        protected function getElementList() 
        {
            $result = '';
            $first = true;
            foreach ($this->value as $element) {
                if (!$first) {
                    $result .= ',';
                }
                if (is_int($element)) {
                    $result .= $this->escape($element);
                } else if (is_object($element)) {
                    $result .= $this->escape($element->getID());
                }
                $first = false;
            }
            return $result.')';
        }
}
