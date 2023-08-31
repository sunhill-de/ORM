<?php

namespace Sunhill\ORM\InfoMarket\InternalMarketeers;

use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;

class InfoMarketVersionItem extends SimpleInfoMarketItem
{
    
    protected static $type = 'varchar';
    
    protected function readItem()
    {
        return '1.0';
    }
}