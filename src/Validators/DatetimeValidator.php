<?php

namespace Sunhill\ORM\Validators;

class DatetimeValidator extends ValidatorBase {
    
    private static function guessDateParts($parts) {
        switch (count($parts)) {
            case 0:
                return null;
                break;
            case 1:
                $test = $parts[0];
                if ($test <= 12) {
                    return array(0,$test,0);
                } elseif ($test <= 31) {
                    return array(0,0,$test);
                } else {
                    return array($test,0,0);
                }
            case 2:
                // es gibt die Kombi tag-monat oder monat-jahr
                if ($parts[0] < $parts[1]) {
                    $parts = array_reverse($parts);
                }
                list($part1,$part2) = $parts; // $part1 ist immer größer gleicht $part2
                if ($part1 > 12) {
                    // Es dürfte sich um eine Jahreszahl handeln
                    return array($part1,$part2,0);
                } else {
                    return array(0,$parts[0],$parts[1]);
                }
            case 3:
                if (is_null($parts[2])) {
                    $parts[2] = 0;
                }
                return $parts;
            default:
                return null;
        }
        
    }
    
    public function isValidDate($test,bool $fill=false) {
        if (is_float($test)) {
            return null;
        }
        if (strpos($test,'.') !== false) {
            $parts = array_reverse(explode('.',$test));
        } elseif (strpos($test,'-') !== false) {
            $parts = explode('-',$test);
        } else {
            if (is_numeric($test)) {
                $parts = array($test);
            } else {
                return null;
            }
        }
        if (!$fill && (count($parts) !== 3)) {
            return null;
        }
        if (($parts = self::guessDateParts($parts)) === null) {
            return null;
        }
        list($year,$month,$day) = $parts;
        if (empty($year)) {
            $year = 0;
        }
        if (!($month == 0) && !($day == 0) && !($year == 0) && !checkdate($month,$day,$year)) {
            return false;
        }
        if (strlen($month) == 1) { $month = '0'.$month; }
        if (strlen($day) == 1) { $day = '0'.$day; }
        if ($year == 0) {
            $year = '0000';
        }
        return "$year-$month-$day";
    }
    
    public function isValidTime($test) {
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

    public function isValidDatetime($test) {
        if (is_numeric($test)) {
            $date = new \DateTime('@'.$test);
            return $date->format('Y-m-d H:i:s');
        }
        $parts = explode(' ',$test);
        if (count($parts) != 2) {
        }
        list($date,$time) = $parts;
        if (!($date = self::isValidDate($date))) {
            return null;
        }
        if (!($time = self::isValidTime($time))) {
            return null;
        }
        return "$date $time";
    }
    
    protected function isValid($value) {
        $result = self::isValidDatetime($value);
        if (is_null($result)) {
            return false;
        }
        return true;
    }
    
    
    protected function prepare(&$test) {
        return $this->isValidDatetime($test);
    }
    
}