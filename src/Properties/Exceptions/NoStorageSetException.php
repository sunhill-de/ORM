<?php

/**
 * @file NoStorageSetException.php
 * Provides the NoStorageSetException that is raisen, when a read or write attemt is performed
 * with no set storage
 * 
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Exceptions;

/**
 * An exception that is raised, when a read or write attemt is performed with no set storage
 * @author lokal
 */
class NoStorageSetException extends PropertyException 
{
}
