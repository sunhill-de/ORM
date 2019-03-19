<?php

namespace Sunhill\Properties;

class oo_property_datetime extends oo_property_datetime_base {

	protected $type = 'datetime';
	
	public static function is_valid_datetime($test) {
		if (is_numeric($test)) {
			$date = new \DateTime('@'.$test);
			return $date->format('Y-m-d H:i:s');
		}
		$parts = explode(' ',$test);
		if (count($parts) != 2) {
		}
		list($date,$time) = $parts;
		if (!($date = self::is_valid_date($date))) {
			return null;
		}
		if (!($time = self::is_valid_time($time))) {
			return null;
		}
		return "$date $time";		
	}
	
	protected function validate($value) {
		$result = self::is_valid_datetime($value);
		if (is_null($result)) {
			throw new InvalidValueException("'$value' ist kein gültiger Zeitstempel.");			
		}
		return $result;
	}
	
	
}