<?php

namespace Sunhill\ORM\InfoMarket\InternalMarketeers;

use Sunhill\ORM\InfoMarket\Marketeer;
use Sunhill\Basic\Base;

class InfoMarketMarketeer extends Marketeer
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setName('infomarket');
        $this->addEntry('name', InfoMarketNameItem::class);
        $this->addEntry('version', InfoMarketVersionItem::class);
    }
}