<?php

/**
 * @file OrmChecks.php
 * An extension to the sunhill check system to perform checks on the sunhill orm database
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-09-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/ORMCheckTest.php
 * Coverage: unknown
 * PSR-Status: complete
 */

namespace Sunhill\ORM\Checks;

use Sunhill\Basic\Checker\Checker;
use Illuminate\Support\Facades\DB;

/**
 * Provides checks for the checking subsystem of sunhill for the orm system
 * @author klaus
 *
 */
class ChecksBase extends Checker 
{
    
    /**
     * Helper function for the check for tables that point to non existing entries
     */
    protected function checkForDanglingPointers(string $master, string $master_field, string $slave, string $slave_field, bool $master_can_be_null=false) 
    {
        $query = DB::table($master.' AS a')->select('a.'.$master_field.' as id')->leftJoin($slave.' AS b','a.'.$master_field,'=','b.'.$slave_field)->whereNull('b.'.$slave_field);    
        if (!$master_can_be_null) {
            $query = $query->where('a.'.$master_field,'>',0);
        }
        $query_result = $query->get();
        return count($query_result);
    }

    protected function repairDanglingPointers(string $master, string $master_field, string $slave, string $slave_field)
    {
        while ($count = DB::table($master.' AS a')->select('a.'.$master_field.' as id')->leftJoin($slave.' AS b','a.'.$master_field,'=','b.'.$slave_field)->whereNull('b.'.$slave_field)->where('a.'.$master_field,'>',0)->delete()) {
            if (!isset($result)) {
                $result = $count;
            }            
        }
        return $result;
    }
}
