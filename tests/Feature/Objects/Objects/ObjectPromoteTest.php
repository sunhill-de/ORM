<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Properties\Exceptions\AttributeException;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;

class ObjectPromoteTest extends DatabaseTestCase
{

    public function testUpgradeDummyToDummychild()
    {
        $test = Objects::load(1);
        $test->promote(DummyChild::class, ['dummychildint'=>543]);
        
        $load = Objects::load(1);
        $this->assertEquals(123, $load->dummyint);
        $this->assertEquals(543, $load->dummychildint);
        $this->assertTrue($load->tags->hasTag('TagA'));
        $this->assertEquals(444, $load->general_attribute);
    }
    
    public function testUpgradeTestParentToTestChild()
    {
        $test = Objects::load(9);
        $test->promote(TestChild::class,
            [
                'childint'=>543,
                'childchar'=>'ADA',
                'childfloat'=>5.34,
                'childtext'=>'Like a rolling stone',
                'childdatetime'=>'2023-10-10 12:34:55',
                'childtime'=>'12:34:55',
                'childdate'=>'2023-10-10',
                'childenum'=>'testA'
            ]);
        $load = Objects::load(9);
        $this->assertEquals(543, $load->childint);
    }
    
    public function testUpgradeTestParentToSimpleChild()
    {
        $test = Objects::load(9);
        $test->promote(TestSimpleChild::class,[]);

        $load = Objects::load(9);
        $this->assertTrue(is_a($load, TestSimpleChild::class));
    }
    
    public function testUgradeReferenceOnlyToThirdLevelChild()
    {
        $test = Objects::load(27);
        $test->promote(ThirdLevelChild::class, [
            'childint'=>111,
            'childchildint'=>222,
            'childchildchar'=>'AAC',
            'thirdlevelsarray'=>['AB','CD']
        ]);

        $load = Objects::load(27);
        $this->assertEquals('Test A', $load->testsarray[0]);
        $this->assertEquals(111, $load->childint);
        $this->assertEquals(222, $load->childchildint);
        $this->assertEquals('CD',$load->thirdlevelsarray[1]);
    }
    
    
}
