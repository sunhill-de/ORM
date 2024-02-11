<?php 
use Sunhill\ORM\Properties\Types\TypeVarchar;
use Sunhill\ORM\Properties\Types\TypeInteger;
use Sunhill\ORM\Properties\Types\TypeBoolean;
use Sunhill\ORM\Properties\Types\TypeDate;
use Sunhill\ORM\Properties\Types\TypeDateTime;
use Sunhill\ORM\Properties\Types\TypeEnum;
use Sunhill\ORM\Properties\Types\TypeFloat;
use Sunhill\ORM\Properties\Types\TypeText;
use Sunhill\ORM\Properties\Types\TypeTime;

return [
    'boolean'=>TypeBoolean::class,
    'bool'=>TypeBoolean::class,
    'date'=>TypeDate::class,
    'datetime'=>TypeDateTime::class,
    'enum'=>TypeEnum::class,
    'float'=>TypeFloat::class,
    'int'=>TypeInteger::class,
    'integer'=>TypeInteger::class,
    'text'=>TypeText::class,
    'time'=>TypeTime::class,
    'varchar'=>TypeVarchar::class,
    'string'=>TypeVarchar::class,
    
];

?>