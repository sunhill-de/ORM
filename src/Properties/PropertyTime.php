<?php

/**
 * @file PropertyTime.php
 * Provides the time property
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Utils\DateTimeCheck;

class PropertyTime extends AtomarProperty {

    use DateTimeCheck;
    
	protected static $type = 'time';

		
}