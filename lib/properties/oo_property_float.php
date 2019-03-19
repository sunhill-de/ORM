<?php

namespace Sunhill\Properties;

class oo_property_float extends oo_property {
	
	protected $type = 'float';
	
	protected $features = ['object','simple'];

	protected $validator_name = 'float_validator';
}