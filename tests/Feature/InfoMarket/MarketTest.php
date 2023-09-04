<?php

namespace Sunhill\ORM\Tests\Feature\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facades\InfoMarket;
use Sunhill\ORM\InfoMarket\InternalMarketeers\InfoMarketMarketeer;

class MarketTest extends TestCase
{
    
    public function testMarket()
    {
        InfoMarket::installMarketeer('infomarket',InfoMarketMarketeer::class);
        
        $info = InfoMarket::getItem('infomarket.name', 'anybody', 'stdclass');
        $this->assertEquals('InfoMarket', $info->value);
    }
    
}