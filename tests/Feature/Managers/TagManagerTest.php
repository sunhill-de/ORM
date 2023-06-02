<?php

namespace Sunhill\ORM\Tests\Feature\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Managers\TagManager;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Objects\Tag;
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Objects\TagException;

class TagManagerTest extends DatabaseTestCase
{

    /**
     * @dataProvider SearchTagProvider
     */
    public function testSearchTag($input, $expected_id, $multiple)
    {
        $result = Tags::searchTag($input);
        if ($multiple && count($result) <2) {
            $this->fail('Multiple results expected.');
        }
        if (is_null($expected_id)) {
            $this->assertEquals(0, count($result));
        } else {
            $this->assertEquals($expected_id,$result[0]->id);
        }
    }

    public function SearchTagProvider()
    {
        return [
            ['TagA',1,false],
            ['TagC',3,false],
            ['TagB.TagC',3,false],
            ['TagE',5,true],
            ['NonExisting',null,false]
        ];
    }

    /**
     * @dataProvider GetTagProvider
     * @param unknown $input
     * @param unknown $expected_id
     * @param unknown $exception
     */
    public function testGetTag($input, $expected_id, $exception)
    {
        if  (!is_null($exception)) {
            $this->expectException($exception);
        }
        if (is_callable($input)) {
            $input = $input();
        }
        $this->assertEquals($expected_id,Tags::getTag($input)->getID());
    }
    
    public function GetTagProvider()
    {
        return [
            [1,1,null],
            [function() { $result = new Tag(); $result->load(2); return $result; }, 2, null ],
            ['TagA',1,null],
            ['TagG',7,null],
            ['TagE',0,TagException::class],
            ['Nonexisting',0,TagException::class]
        ];
    }
    
    public function testLoadTagPass()
    {
        $this->assertEquals(2,Tags::loadTag(2)->getID());
    }
    
    public function testLoadTagFail()
    {
        $this->assertEquals(null,Tags::loadTag(1000));
    }
    
    public function testTagQuery()
    {
        $this->assertEquals(1, Tags::query()->where('name','TagA')->first()->id);
    }
}
