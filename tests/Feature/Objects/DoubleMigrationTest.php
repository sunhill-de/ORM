<?php

namespace Sunhill\ORM\Tests\Feature\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\PropertyList;
use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;

class DoubleMigrationObject extends ORMObject
{
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->varchar('name')
             ->setMaxLen(100)
             ->searchable();
    }
 
    protected static function setupInfos()
    {
        static::addInfo('name','DoubleMigrationObject');
        static::addInfo('table','doublemigrationobjects');
    }
    
}

class DoubleMigrationTest extends DatabaseTestCase
{

    public function testDoubleMigration()
    {
        Classes::registerClass(DoubleMigrationObject::class);
        DoubleMigrationObject::migrate();       
        DoubleMigrationObject::migrate();
        
        $this->assertFalse(Schema::hasColumn('doublemigrationobjects','_uuid'));
    }
    
}