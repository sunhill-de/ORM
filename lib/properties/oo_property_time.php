<?php

namespace Sunhill\Properties;

class oo_property_time extends oo_property_field {

	protected $type = 'time';

	protected $features = ['object','simple'];
	
	protected $validator_name = 'time_validator';
		
}