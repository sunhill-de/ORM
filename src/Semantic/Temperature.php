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

class Temperature extends Semantic
{
    protected static $name = 'Temperature';
    
    protected static $unit = 'Degreecelsius';
    
}
 