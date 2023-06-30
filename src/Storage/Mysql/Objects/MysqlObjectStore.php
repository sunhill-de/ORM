<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionStore;

class MysqlObjectStore extends MysqlCollectionStore
{

    protected function storeAttributes()
    {
    }
    
    public function run()
    {
        parent::run();
        $this->storeAttributes();
    }
    
    public function handlePropertyInformation($property)
    {
    }
    
    public function handlePropertyTags($property)
    {
    }
    
}