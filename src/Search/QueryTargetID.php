<?php

/**
 * @file QueryTargerID.php
 * Provides the QueryTargetID class
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Search;

class QueryTargetID extends query_target 
{
    
    public function getQueryPart() 
    {
        return 'select '.$this->table_id.'.id';
    }
}