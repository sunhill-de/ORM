<?php

namespace Sunhill\Search;

class query_target_id extends query_target {
    
    public function get_query_part() {
        return 'select a.id';
    }
}