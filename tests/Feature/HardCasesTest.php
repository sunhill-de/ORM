<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Utils\ObjectList;

class HardCasesTest extends DatabaseTestCase
{
    
    protected function simplify_result(ObjectList $result) {
        $return = [];
        for($i=0;$i<count($result);$i++) {
            $return[] = $result[$i]->getID($i);
        }
        return $return;
    }
    
    /**
     * 25 is not in the results although it should be
     */
    public function testSimpleSearchIDs() {
        $result = $this->simplify_result(TestChild::search()->where("childoarray","none of",[1,2,3])->get());
        $this->assertEquals([17,18,19,21,22,23,25],$result);
    }
    
}