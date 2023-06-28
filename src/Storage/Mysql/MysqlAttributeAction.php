<?php
/**
 * @file AttributeAction.php
 * Capsulates the AttributeQuery into an action
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2023-06-28
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Properties\Property;
use Illuminate\Testing\Assert as PHPUnit;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Storage\Exceptions\PropertyNotFoundException;

/**
 * 
 * @author lokal
 *
 */
class MysqlAttributeAction
{
    
    public function run()
    {
        return new MysqlAttributeQuery();
    }

}
