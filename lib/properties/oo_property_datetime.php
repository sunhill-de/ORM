<?php

namespace Sunhill\Properties;

class oo_property_datetime extends oo_property {

	protected $type = 'datetime';
	
	protected $features = ['object','simple'];
	
    protected $validator_name = 'datetime_validator';
    
}