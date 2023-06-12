<?php
/**
 * @file StorageSupport.php
 * The implementation of StorageSupportBase for databases
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2023-06-02
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage\Mysql;

class MysqlStorageSupport
{
    public function tagQuery()
    {
        return new MysqlTagQuery();
    }
    
    public function attributeQuery()
    {
        return new MysqlAttributeQuery();
    }
}