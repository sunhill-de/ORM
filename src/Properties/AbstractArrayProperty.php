<?php
/**
 * @file AbstractArrayProperty.php
 * Defines an abstract property as base for all array like properties
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\ORM\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\ORM\Properties\Exceptions\NoUserManagerInstalledException;

abstract class AbstractArrayProperty extends AbstractProperty implements \ArrayAccess,\Countable,\Iterator
{
       
    public function getAccessType(): string
    {
        return 'array';
    }
    
}