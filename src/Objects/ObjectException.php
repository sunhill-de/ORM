<?php
/**
 * @file ObjectException.php
 * Provides the base exception for objects
 * Lang en (complete)
 * Reviewstatus: 2021-10-06
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 * Dependencies: Objects, ORMException, base
 * PSR-State: complete
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\ORMException;

/**
 * Baseclass for errors that raise inside of ORMObject
 * @author lokal
 */
class ObjectException extends ORMException 
{
}
