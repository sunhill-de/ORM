<?php

/**
 * @file QueryTargerCount.php
 * Provides the QueryTargerCount class
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

class QueryTargetCount extends QueryTarget 
{
    
    public function getQueryPart() 
    {
        return 'select count(a.id) as count';
    }
}