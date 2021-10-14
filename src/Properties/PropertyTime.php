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

class PropertyTime extends PropertyField {

	protected $type = 'time';

	protected $features = ['object','simple'];
	
	protected $validator_name = 'time_validator';
		
}