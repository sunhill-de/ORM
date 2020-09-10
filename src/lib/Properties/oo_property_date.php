<?php

namespace Sunhill\ORM\Properties;

class oo_property_date extends oo_property_field {
	
	protected $type = 'date';
	
	protected $features = ['object','simple'];
	
    protected $validator_name = 'date_validator';	
}