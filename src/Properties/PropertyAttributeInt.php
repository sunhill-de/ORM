<?php

namespace Sunhill\ORM\Properties;

class PropertyAttributeInt extends PropertyAttribute {
	
	protected static $type = 'AttributeInt';

	protected $validator_name = 'IntValidator';

}