<?php
/**
 * @file ClassNotARelativeException.php
 * Is raised when a degration or promotion is done to a class that is not a relative to the class before
 * 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-04-27
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: none
 */

namespace Sunhill\ORM\Storage\Exceptions;

/**
 * Exceptions inside storages should be a direct or indirect initiation of StorageException
 * @author Klaus
 *
 */
class ClassNotARelativeException extends StorageException {}

