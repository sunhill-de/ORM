<?php
/**
 * @file tests/Unit/Checks/ObjectCheckTest.php
 * Tests the routine in ObjectChecks
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Checks\ObjectChecks;
use Sunhill\Basic\Facades\Checks;
use Sunhill\Basic\Checker\CheckException;
use Sunhill\Basic\Checker\Checker;

class ObjectChecksTest extends CheckTestCase
{
    
    /**
     * Tests: processTable
     */
    public function testProcessTable()
    {
        $checker = new ObjectChecks();
        $missing = [];
        $matrix = [
            'dummy'=>['table'=>'dummies','parent'=>'object'],
            'object'=>['table'=>'objects','parent'=>''],
        ];
        $this->callProtectedMethod($checker, 'processTable', [$matrix, 'dummy', &$missing]);
        $this->assertTrue(empty($missing));
        DB::table('objects')->where('id',1)->delete();
        $this->callProtectedMethod($checker, 'processTable', [$matrix, 'dummy', &$missing]);
        $this->assertFalse(empty($missing));
    }
    
    /**
     * @dataProvider repairableProblemProvider
     * @param unknown $check
     * @param unknown $destroy_callback
     */
    public function testRepairableProblems($check, $destroy_callback)
    {
        $checker = new ObjectChecks();
        $this->assertEquals('passed',$this->runCheck($checker, $check, false));
        $destroy_callback();
        $this->assertEquals('failed',$this->runCheck($checker, $check, false));
        $this->assertEquals('repaired',$this->runCheck($checker, $check, true));
        $this->assertEquals('passed',$this->runCheck($checker, $check, false));
    }
    
    /**
     * Tests: see List
     */
    public function repairableProblemProvider()
    {
        return [
            ['check_EveryObjectHasAParentEntry', function() { DB::table('dummies')->where('id',5)->delete(); }],
            ['check_ObjectObjectAssignsContainerExist', function() { DB::table('objects')->where('id',14)->delete(); }],            
            ['check_ObjectObjectAssignsElementExist', function() { DB::table('objects')->where('id',7)->delete(); }],
            ];
    }
    
    
}