<?php

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\Basic\Tests\Scenario\ScenarioWithDatabase;
use Sunhill\Basic\Tests\Scenario\ScenarioWithTables;

class StdReadOnlyScenario extends ScenarioBase{

        use ScenarioWithFiles,ScenarioWithDirs,ScenarioWithLinks,
            ScenarioWithDatabase,ScenarioWithTables;
        
    protected $Requirements = [
        'Database'=>[
            'destructive'=>false,
        ],        
        'Tables'=>[
            'destructive'=>false,
        ],        
    ];
  
    protected function GetDatabase() {
      return [
                'objects'=>[
                    'id int auto_increment primary key',
                    'classname varchar(100)',
                    'created_at datetime',
                    'updated_at timestamp'
                ],
                'objectobjectassigns'=>[
                    'container_id int primary key',
                    'element_id int primary key',
                    'field varchar(50) primary key',
                    'index int'
                ],
                'stringobjectassigns'=>[
                    'container_id int primary key',
                    'element_id varchar(200) primary key',
                    'field varchar(50) primary key',
                    'index int'
                ],
                'tags'=>[
                    'id int auto_increment primary key',
                    'created_at datetime',
                    'updated_at timestamp',
                    'name varchar(100)',
                    'options defaults 0',
                    'parent_id unsigned int defaults null'
                ],        
                'tagobjectassigns'=>[
                    'container_id int primary key',
                    'tag_id int primary key',
                ],
                'tagcache'=>[
                    'id int auto_increment primary key',
                    'name varchar(150)',
                    'tag_id int',
                    'created_at datetime',
                    'updated_at timestamp'
                ],
                'externalhooks'=>[
                    'id int auto_increment primary key',
                    'container_id int primary key',
                    'target_id int primary key',
                    'action varchar(100)',
                    'subaction varchar(100)',
                    'hook varchar(100)',
                    'payload varchar(100)',
                ],
                'caching'=>[
                    'id int auto_increment primary key',
                    'object_id int',
                    'fieldname varchar(50)',
                    'value varchar(100)'
                ],
                'attributes'=>[
                    'id int auto_increment primary key',
                    'name varchar(100)',
                    'allowed_objects varchar(100)',
                    'type varchar(100)',
                    'property varchar(100)',
                    'created_at datetime',
                    'updated_at timestamp'
                ],
                'attributevalues'=>[
                    'attribute_id int',
                    'object_id int',
                    'value varchar(200)',
                    'textvalue text',
                    'created_at datetime',
                    'updated_at timestamp'
                ],
                'objectunits'=>[
                    'id int',
                    'intvalue int'
                ],
                'passthrus'=>[
                    'id int',
                ],
                'referenceonlies'=>[
                    'id int',
                    'testint int'
                ],
                'secondlevelchildren'=>[
                    'id int',
                    'childint int'                  
                ],
        
      ];
    }
  
    protected function GetTables() {
      return [];
    }
  
}
