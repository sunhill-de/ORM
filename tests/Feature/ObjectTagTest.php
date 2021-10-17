<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;

class ObjectTagTest extends DBTestCase
{
    
    /**
     * @dataProvider TagProvider
     */
    public function testTagObject($set,$expect,$create) {
         $test = new Dummy();
        
        for ($i=0;$i<count($set);$i++) {
            if ($expect[$i]=='except') {
                try {
                    $tag = new Tag($set[$i],$create);
                } catch (\Exception $e) {
                    $this->assertTrue(true);
                    return;
                }
                $this->fail();
            } else {
                $tag = new Tag($set[$i],$create);
            }
            $test->tags->stick($tag);
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

    public function TagProvider() {
        return [[['TagA'],['TagA'],false],
                [['TagB.TagC'],['TagB.TagC'],false],
                [['NewTag'],['NewTag'],true],
                [['NewTag'],['except'],false],
                [['TagA','TagB'],['TagA','TagB'],false]
        ];
    }
    
    /**
     * @dataProvider ChangeTagProvider
     * @group change
     */
    public function testChangeTags($init,$add,$delete,$expect,$changestr) {
        $test = new Dummy();        
        for ($i=0;$i<count($init);$i++) {
            $tag = new Tag($init[$i],true);
            $test->tags->stick($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        
        Objects::flushCache();
        $read =  new Dummy();
        $read = Objects::load($test->getID());
        for ($i=0;$i<count($add);$i++) {
            $tag = new Tag($add[$i],true);
            $read->tags->stick($tag);            
        }
        for ($i=0;$i<count($delete);$i++) {
            $tag = new Tag($delete[$i],true);
            $read->tags->remove($tag);            
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
    
    /**
     * @dataProvider ChangeTagProvider
     * @group Trigger
     */
    public function testChangeTagsTrigger($init,$add,$delete,$expect,$changestr) {
        $test = new Dummy();
        for ($i=0;$i<count($init);$i++) {
            $tag = new Tag($init[$i],true);
            $test->tags->stick($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        
        Objects::flushCache();
        $read = Objects::load($test->getID());
        for ($i=0;$i<count($add);$i++) {
            $tag = new Tag($add[$i],true);
            $read->tags->stick($tag);
        }
        for ($i=0;$i<count($delete);$i++) {
            $tag = new Tag($delete[$i],true);
            $read->tags->remove($tag);
        }
        $read->commit();
        $this->assertEquals($changestr,$read->changestr);                
    }
    
    public function ChangeTagProvider() {
        return [
            [['TagA','TagB'],['TagChildA'],[],['TagA','TagB','TagChildA'],'ADD:TagChildA'],
            [['TagA','TagB'],[],['TagA'],['TagB'],'REMOVED:TagA'],
            [['TagA','TagB'],['TagChildA'],['TagA'],['TagB','TagChildA'],'ADD:TagChildAREMOVED:TagA'],
            [['TagA'],['TagC'],[],['TagA','TagB.TagC'],'ADD:TagB.TagC'],
            [['TagA','TagB.TagC'],[],['TagC'],['TagA'],'REMOVED:TagB.TagC'],
        ];
    }
}
