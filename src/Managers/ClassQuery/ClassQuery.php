<?php

/**
 * @file ClassQuery.php
 * Provides the ClassQuery for querying classes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-23
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Managers\ClassQuery;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Query\ArrayQuery;

class ClassQuery extends ArrayQuery
{
    
    protected function getRawData()
    {
        return Classes::getAllClasses();
    }
    
    public function __construct()
    {
        $this->condition_builder = new ClassConditionBuilder();    
    }
    
    public function whereHasPropertyOfType(string $type, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->where('property','has own type',$type);
        } else {
            $this->condition_builder->where('property','has type',$type);            
        }
        return $this;    
    }
    
    public function orWhereHasPropertyOfType(string $type, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->orWhere('property','has own type',$type);
        } else {
            $this->condition_builder->orWhere('property','has type',$type);
        }
        return $this;        
    }
    
    public function whereNotHasPropertyOfType(string $type, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->whereNot('property','has own type',$type);
        } else {
            $this->condition_builder->whereNot('property','has type',$type);
        }
        return $this;        
    }
    
    public function orWhereNotHasPropertyOfType(string $type, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->orWhereNot('property','has own type',$type);
        } else {
            $this->condition_builder->orWhereNot('property','has type',$type);
        }
        return $this;        
    }
    
    public function whereHasPropertyOfName(string $name, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->where('property','has own name',$name);
        } else {
            $this->condition_builder->where('property','has name',$name);
        }
        return $this;
    }
    
    public function orWhereHasPropertyOfName(string $name, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->orWhere('property','has own name',$name);
        } else {
            $this->condition_builder->orWhere('property','has name',$name);
        }
        return $this;
    }
    
    public function whereNotHasPropertyOfName(string $name, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->whereNot('property','has own name',$name);
        } else {
            $this->condition_builder->whereNot('property','has name',$name);
        }
        return $this;
    }
    
    public function orWhereNotHasPropertyOfName(string $name, bool $not_inhertied = false): ClassQuery
    {
        if ($not_inhertied) {
            $this->condition_builder->orWhereNot('property','has own name',$name);
        } else {
            $this->condition_builder->orWhereNot('property','has name',$name);
        }
        return $this;
    }
    
    public function whereHasParent(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->where('parent','has direct',$name);            
        } else {
            $this->condition_builder->where('parent','has',$name);
        }
        return $this;
    }
    
    public function orWhereHasParent(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->orWhere('parent','has direct',$name);
        } else {
            $this->condition_builder->orWhere('parent','has',$name);
        }
        return $this;
    }
    
    public function whereNotHasParent(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->whereNot('parent','has direct',$name);
        } else {
            $this->condition_builder->whereNot('parent','has',$name);
        }
        return $this;
    }
    
    public function orWhereNotHasParent(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->orWhereNot('parent','has direct',$name);
        } else {
            $this->condition_builder->orWhereNot('parent','has',$name);
        }
        return $this;
    }
    
    public function whereIsParentOf(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->where('parent','is direct',$name);
        } else {
            $this->condition_builder->where('parent','is',$name);            
        }
        return $this;
    }
    
    public function orWhereIsParentOf(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->orWhere('parent','is direct',$name);
        } else {
            $this->condition_builder->orWhere('parent','is',$name);
        }
        return $this;
    }
    
    public function whereNotIsParentOf(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->whereNot('parent','is direct',$name);
        } else {
            $this->condition_builder->whereNot('parent','is',$name);
        }
        return $this;
    }
    
    public function orWhereNotIsParentOf(string $name, bool $only_direct = false): ClassQuery
    {
        if ($only_direct) {
            $this->condition_builder->orWhereNot('parent','is direct',$name);
        } else {
            $this->condition_builder->orWhereNot('parent','is',$name);
        }
        return $this;
    }
    
 }