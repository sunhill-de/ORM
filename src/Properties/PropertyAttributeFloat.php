<?php

namespace Sunhill\ORM\Properties;

class PropertyAttributeFloat extends PropertyAttribute {
	
	protected static $type = 'attribute_float';
	
	protected $validator_name = 'FloatValidator';
	
}