<?php
/**
 * @file PointInTime.php
 * A semantic class for describing that a value marks a point in time
 * Usually this is für Date, DateTime and Time properties
 * Lang de,en
 * Reviewstatus: 2023-05-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Semantic;

class PointInTime extends SemanticInTime
{
    protected static $name = 'Point in time';
    
}
 