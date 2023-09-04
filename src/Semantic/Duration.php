<?php
/**
 * @file Duration.php
 * A semantic class for describing a duration of time. Normally measured in seconds
 * Lang de,en
 * Reviewstatus: 2023-05-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Semantic;

use Sunhill\ORM\Units\Second;

class Duration extends SemanticInTime
{
    protected static $name = 'Duration';
    
    protected static $unit = 'Second';
    
    /**
     * With this method it is possible to define a conversion to display a raw value in a human readable one
     * @return string: The human readable representation of value
     */
    public static function processHumanReadableValue($timespan, string $human_readable_unit = ''): string
    {
        $seconds = $timespan%60;
        $timespan = intdiv($timespan,60);
        $minutes = $timespan%60;
        $timespan = intdiv($timespan,60);
        $hours = $timespan%24;
        $timespan = intdiv($timespan,24);
        $days = $timespan%365;
        $years = intdiv($timespan,365);
        if ($years > 0) {
            return $years.' '.(($years == 1)?static::translate('year'):static::translate('years')).
            ' '.static::translate('and').' '.$days.' '.(($days == 1)?static::translate('day'):static::translate('days'));
        } elseif ($days > 0) {
            return $days.' '.(($days == 1)?static::translate('day'):static::translate('days')).
            ' '.static::translate('and').' '.$hours.' '.(($hours == 1)?static::translate('hour'):static::translate('hours'));
        } elseif ($hours > 0) {
            return $hours.' '.(($hours == 1)?static::translate('hour'):static::translate('hours')).
            ' '.static::translate('and').' '.$minutes.' '.(($minutes == 1)?static::translate('minute'):static::translate('minutes'));
        } elseif ($minutes > 0) {
            return $minutes.' '.(($minutes == 1)?static::translate('minute'):static::translate('minutes')).
            ' '.static::translate('and').' '.$seconds.' '.(($seconds == 1)?static::translate('second'):static::translate('seconds'));
        } else {
            return $seconds.' '.(($seconds == 1)?static::translate('second'):static::translate('seconds'));
        }
    }
    
}
 