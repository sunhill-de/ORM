<?php

namespace Sunhill\Objects;

class oo_property_datetime_base extends oo_property {
	
	public static function is_valid_date($test) {
	  try {	
		if (strpos($test,'.') !== false) {			
			$parts = explode('.',$test);
			if (count($parts) != 3) {
				return false;
			}
			list($day,$month,$year) = $parts;
		} else if (strpos($test,'-') !== false) {
			list($year,$month,$day) = explode('-',$test);
		}
	  } catch (\Exception $e) {
	  	return false;	  	
	  }
	  if (!checkdate($month,$day,$year)) {
	  	return false;
	  }
	  if (strlen($month) == 1) { $month = '0'.$month; }
	  if (strlen($day) == 1) { $day = '0'.$day; }
	  return "$year-$month-$day";
	}

	public static function is_valid_time($test) {
		$parts = explode(':',$test);
		switch (count($parts)) {
			case 2: 
				list($hour,$minute) = $parts;
				$second = '00'; break;
			case 3:
				list($hour,$minute,$second) = $parts;
				break;
			default:
				return false;
		}
		if (empty($hour) || empty($minute) || empty($second)) {
			return false;
		}
		if (($hour < 0) || ($hour > 24) || ($minute < 0) || ($minute > 59) || ($second < 0) || ($second > 59))
		{
			return false;
		}
		if (strlen($hour) == 1) { $hour = '0'.$hour; }
		if (strlen($minute) == 1) { $minute = '0'.$minute; }
		if (strlen($second) == 1) { $second = '0'.$second; }
		return "$hour:$minute:$second";
	}
}