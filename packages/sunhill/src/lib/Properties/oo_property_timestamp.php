<?php

namespace Sunhill\Properties;

class oo_property_timestamp extends oo_property_field {

	protected $type = 'timestamp';
	
	protected $features = ['object','complex'];

	protected $initialized = true;
}