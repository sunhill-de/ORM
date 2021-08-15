<?php
/**
 * @file StorageException.php
 * A basic exception that are raised inside storages
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: none
 * Coverage: none
 */

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\ORMException;

/**
 * Exceptions inside storages should be a direct or indirect initiation of StorageException
 * @author Klaus
 *
 */
class StorageException extends ORMException {}

