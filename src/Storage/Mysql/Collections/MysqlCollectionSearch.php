<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

class MysqlCollectionSearch
{
    
    use ClassTables;
    
    public function __construct(public $storage) {}

    protected $id = 0;
    
    public function doSearch()
    {
    }
    
}