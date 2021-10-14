<?php

namespace Sunhill\ORM\Properties;

class PropertyInteger extends PropertyField {
	
	protected $type = 'integer';

	protected $features = ['object','simple'];
	
	protected $validator_name = 'int_validator';

}