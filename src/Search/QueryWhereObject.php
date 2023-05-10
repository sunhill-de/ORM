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
                        $result .= " ".$this->alias.'.'.$this->field." is null ";
                    } else {
                        $value = $this->getValue();
                        $result .= " ".$this->alias.'.'.$this->field." = $value ";
                    }
                    break;
                case '<>':
                case '!=':
                    if (is_null($this->value)) {
                        $result .= " ".$this->alias.'.'.$this->field." is not null ";
                    } else {
                        $value = $this->getValue();
                        $result .= " ".$this->alias.'.'.$this->field." != $value ";                        
                    }
                    break;
                case 'in':
                    $element_list = $this->getElementList();
                    $result .= " ".$this->alias.'.'.$this->field." in ($element_list";
                    break;
                case 'not in':
                    $element_list = $this->getElementList();
                    $result .= " ".$this->alias.'.'.$this->field." not in ($element_list";
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
