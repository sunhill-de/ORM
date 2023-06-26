<?php

/**
 * @file CollectionClassDoesntExistException.php
 * Is raised whenn Collection::loadCollection() is called with a non callabe collection class
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-25
 * Localization: not necessary
 * Documentation: complete
 * Tests: none
 * Coverage: none
 */
namespace Sunhill\ORM\Managers\Exceptions;

class CollectionClassDoesntExistException extends CollectionManagerException {}