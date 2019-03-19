<?php

namespace Sunhill\Properties;

class oo_property_integer extends oo_property {
	
	protected $type = 'integer';

	protected $features = ['object','simple'];
	
	protected $validator_name = 'int_validator';

}