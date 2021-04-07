<?php

namespace Sunhill\ORM\Properties;

class oo_property_datetime extends oo_property_field {

	protected $type = 'datetime';
	
	protected $features = ['object','simple'];
	
    protected $validator_name = 'datetime_validator';
    
}