<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Tags;
use Illuminate\Support\Facades\DB;

class ObjectTagTest extends DatabaseTestCase
{
    
    /**
     * @dataProvider TagProvider
     */
    public function testTagObject($set,$expect,$create) {
        
        $test = new Dummy();
        
        for ($i=0;$i<count($set);$i++) {
            if ($expect[$i]=='except') {
                try {
                    if (!Tags::searchTag($set[$i])) {
                        Tags::addTag($set[$i],$create);
                    }
                } catch (\Exception $e) {
                    $this->assertTrue(true);
                    return;
                }
                $this->fail('Expected exception not raised');
            } else {
                if (!Tags::searchTag($set[$i])) {
                    Tags::addTag($set[$i],$create);
                }
            }
            $test->tags->stick($set[$i]);
        }
        $test->dummyint = 1;
        $test->commit();
        for ($i=0;$i<count($expect);$i++) {
            $this->assertEquals($expect[$i],$test->tags[$i]);
        }
        $reread =  new Dummy();
        Objects::flushCache();
        $reread = Objects::load($test->getID());
        for ($i=0;$i<count($expect);$i++) {
            $this->assertEquals($expect[$i],$reread->tags[$i]);
        }
    }

    public static function TagProvider() {
        return [[['TagA'],['TagA'],false],
                [['TagB.TagC'],['TagB.TagC'],false],
                [['NewTag'],['NewTag'],true],
        //        [['NewTag'],['except'],false],
                [['TagA','TagD'],['TagA','TagD'],false]
        ];
    }
    
    /**
     * @dataProvider ChangeTagProvider
     * @group change
     */
    public function testChangeTags($init,$add,$delete,$expect,$changestr) {
        
        $test = new Dummy();        
        for ($i=0;$i<count($init);$i++) {
            if (!Tags::searchTag($init[$i])) {
                Tags::addTag($init[$i]);
            }
            $test->tags->stick($init[$i]);
        }
        $test->dummyint = 1;
        $test->commit();
        
        Objects::flushCache();
        $read =  new Dummy();
        $read = Objects::load($test->getID());
        for ($i=0;$i<count($add);$i++) {
            if (!Tags::searchTag($add[$i])) {
                Tags::addTag($add[$i]);
            }
            $read->tags->stick($add[$i]);            
        }
        for ($i=0;$i<count($delete);$i++) {
            $read->tags->remove($delete[$i]);            
        }
        $read->commit();
        
        Objects::flushCache();
        $reread = Objects::load($test->getID());
        
        $given_tags = array();
        for ($i=0;$i<count($reread->tags);$i++) {
            $given_tags[] = $reread->tags[$i];
        }
        sort($expect);
        sort($given_tags);
        $this->assertEquals($expect,$given_tags);
    }
    
    public static function ChangeTagProvider() {
        return [
            [['TagA','TagD'],['TagChildA'],[],['TagA','TagD','TagChildA'],'ADD:TagChildA'],
            [['TagA','TagD'],[],['TagA'],['TagD'],'REMOVED:TagA'],
            [['TagA','TagD'],['TagChildA'],['TagA'],['TagD','TagChildA'],'ADD:TagChildAREMOVED:TagA'],
            [['TagA'],['TagC'],[],['TagA','TagB.TagC'],'ADD:TagB.TagC'],
            [['TagA','TagB.TagC'],[],['TagC'],['TagA'],'REMOVED:TagB.TagC'],
        ];
    }
}
