<?php

/**
 * @file PropertyKeyfield.php
 * Provides a keyfield property
 * Lang en
 * Reviewstatus: 2023-06-14
 * Localization: none
 * Documentation: incomplete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Properties;

class PropertyKeyfield extends AtomarProperty 
{
	
	protected static $type = 'none';
	
	protected $build_rule = '';
	
    public function setBuildRule(string $rule): PropertyKeyfield
    {
        $this->build_rule = $rule;
        return $this;
    }
	
}