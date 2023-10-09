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
    
    public function testGetOffer()
    {
        InfoMarket::installMarketeer('infomarket',InfoMarketMarketeer::class);
        
        $offer = InfoMarket::getOffer('', 'anybody', 'stdclass');
        $this->assertEquals('infomarket', $offer[0]);
        
        $offer = InfoMarket::getOffer('infomarket', 'anybody', 'stdclass');
        $this->assertEquals(['name','version'], $offer);
        
    }
}