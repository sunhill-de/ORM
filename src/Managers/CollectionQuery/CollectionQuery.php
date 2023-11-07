<?php

/**
 * @file CollectionQuery.php
 * Provides the CollectionQuery for querying collections
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-11-05
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/CollectionManagerTest.php
 * Coverage: 
 */
namespace Sunhill\ORM\Managers\CollectionQuery;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Query\ArrayQuery;
use Sunhill\ORM\Facades\Collections;

class CollectionQuery extends ArrayQuery
{
    
    protected function getRawData()
    {
        return Collections::getAllCollections();
    }
    
    public function __construct()
    {
        $this->condition_builder = new CollectionConditionBuilder();    
    }
    
    public function whereHasPropertyOfType(string $type): CollectionQuery
    {
        $this->condition_builder->where('property','has type',$type);            
        return $this;    
    }
    
    public function orWhereHasPropertyOfType(string $type): CollectionQuery
    {
        $this->condition_builder->orWhere('property','has type',$type);
        return $this;        
    }
    
    public function whereNotHasPropertyOfType(string $type): CollectionQuery
    {
        $this->condition_builder->whereNot('property','has type',$type);
        return $this;        
    }
    
    public function orWhereNotHasPropertyOfType(string $type): CollectionQuery
    {
        $this->condition_builder->orWhereNot('property','has type',$type);
        return $this;        
    }
    
    public function whereHasPropertyOfName(string $name): CollectionQuery
    {
        $this->condition_builder->where('property','has name',$name);
        return $this;
    }
    
    public function orWhereHasPropertyOfName(string $name): CollectionQuery
    {
        $this->condition_builder->orWhere('property','has name',$name);
        return $this;
    }
    
    public function whereNotHasPropertyOfName(string $name): CollectionQuery
    {
        $this->condition_builder->whereNot('property','has name',$name);
        return $this;
    }
    
    public function orWhereNotHasPropertyOfName(string $name): CollectionQuery
    {
        $this->condition_builder->orWhereNot('property','has name',$name);
        return $this;
    }
        
 }