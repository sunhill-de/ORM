<?php

namespace Sunhill\ORM\InfoMarket\InternalMarketeers;

use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;

class InfoMarketNameItem extends SimpleInfoMarketItem
{
    
    protected static $type = 'varchar';
    
    protected function readItem()
    {
        return 'InfoMarket';
    }
}