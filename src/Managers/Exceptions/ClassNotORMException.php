<?php

/**
 * @file ClassNotORMException.php
 * This exception is raised when a class to register is not a descendant of ORMObject
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-05-29
 * Localization: not necessary
 * Documentation: complete
 * Tests: none
 * Coverage: none
 */
namespace Sunhill\ORM\Managers\Exceptions;

class ClassNotORMException extends ClassManagerException {}