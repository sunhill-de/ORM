<?php
/**
 * @file Name.php
 * Defines a derived varchar that represents a name
 * Lang de,en
 * Reviewstatus: 2024-03-01
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Semantics;

use Sunhill\ORM\Properties\Types\TypeVarchar;

class Name extends TypeVarchar
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public function getSemantic(): string
    {
        return 'name';
    }
    
    /**
     * Returns the group of the semantic of this property (like time, distance, area, etc.)
     *
     * @return string
     */
    public function getSemanticGroup(): string
    {
        return 'name';
    }
        
}