<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Search\QueryException;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Utils\ObjectList;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\ORMException;

use Sunhill\ORM\Tests\Testobjects\CalcClass;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;

class ObjectSearchTest extends DatabaseTestCase
{

    public function testSimple()
    {
        $result = Dummy::search()->where('dummyint',123);
        $this->assertEquals(3, $result->count());
        $this->assertEquals(1, $result->get()[0]->id);
        $this->assertEquals(1, $result->getObjects()[0]->getID());
    }
}
