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
        
        $this->assertEquals('InfoMarket', InfoMarket::getItemValue('infomarket.name'));
        $info = InfoMarket::getItem('infomarket.name', 'stdclass');
        $this->assertEquals('InfoMarket', $info->value);
    }
    
}