<?php

namespace Sunhill\Properties;

class oo_property_date extends oo_property {
	
	protected $type = 'date';
	
	protected $features = ['object','simple'];
	
    protected $validator_name = 'date_validator';	
}