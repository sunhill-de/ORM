<?php
/**
 * @file StorageSupportBase.php
 * An abstract basic class for support methods (tags, attributes)
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

abstract class StorageSupportBase
{
    abstract public function tagQuery();
    abstract public function attributeQuery();
}