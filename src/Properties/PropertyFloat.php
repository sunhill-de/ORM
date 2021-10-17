<?php

namespace Sunhill\ORM\Properties;

class PropertyFloat extends PropertyField {
	
	protected $type = 'float';
	
	protected $features = ['object','simple'];

	protected $validator_name = 'FloatValidator';
}