<?php

namespace Tests\Unit;

use Tests\TestCase;
use Sunhill\Search\query_builder;
use Sunhill\Search\query_atom;

class QueryBuilderTest extends TestCase
{
    public function testSimpleQuery() {
        $query = new query_builder('\Sunhill\Objects\oo_object');
        $result = $query->get(true);
        $this->assertEquals('select count(a.id) from oo_object as a',$result);
    }
}
