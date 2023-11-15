<?php

namespace Sunhill\ORM\InfoMarket\InternalMarketeers;

use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;

class InfoMarketVersionItem extends SimpleInfoMarketItem
{
    
    protected static $type = 'string';
    
    protected static $item_unit = 'None';
    
    protected static $item_semantic = 'Name';
    
    protected static $item_readable = true;
    
    protected static $item_writeable = false;
    
    protected function readItem()
    {
        return '1.0';
    }
}