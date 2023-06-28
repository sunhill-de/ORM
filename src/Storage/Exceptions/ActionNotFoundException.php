<?php
/**
 * @file ActionNotFoundException.php
 * Is raised when an unknown storage action is called
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
class ActionNotFoundException extends StorageException {}

