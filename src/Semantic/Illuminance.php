<?php
/**
 * @file Temperature.php
 * A semantic class for describing a temperature of a thing
 * Lang de,en
 * Reviewstatus: 2023-05-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Semantic;

use Sunhill\ORM\Units\Second;

class Illuminance extends Semantic
{
    protected static $name = 'Illuminance';
    
    protected static $unit = 'Lux';
    
}
 