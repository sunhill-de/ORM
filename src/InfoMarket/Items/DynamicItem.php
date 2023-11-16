<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\Properties\AtomarProperty;
use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotWriteableException;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;

class DynamicItem extends AtomarProperty
{

    public function defineValue($value)
    {
        $this->doSetValue($value);
        return $this;
    }
    
    public function type(string $type)
    {
        static::$type = $type;
        return $this;
    }
}
