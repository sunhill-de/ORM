<?php

namespace Sunhill\ORM\Search;

class query_target_count extends query_target {
    
    public function get_query_part() {
        return 'select count(a.id) as count';
    }
}