<?php

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\Basic\Tests\Scenario\ScenarioWithDatabase;
use Sunhill\Basic\Tests\Scenario\ScenarioWithTables;

class StdScenarioBase extends ScenarioBase{

    use ScenarioWithDatabase,ScenarioWithTables;
        
    protected function GetDatabase() {
      return [
                'objects'=>[
                    'id int auto_increment primary key',
                    'classname varchar(100)',
                    'created_at datetime default null',
                    'updated_at timestamp default null'
                ],
                'objectobjectassigns'=>[
                    'container_id int',
                    'element_id int',
                    'field varchar(50)',
                    'index int',
                    'PRIMARY KEY (container_id,element_id,field)'
                ],
                'stringobjectassigns'=>[
                    'container_id int primary key',
                    'element_id varchar(200) primary key',
                    'field varchar(50) primary key',
                    'index int',
                    'PRIMARY KEY (container_id,element_id,field)'
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
                    'container_id int',
                    'tag_id int',
                    'PRIMARY KEY (container_id,tag_id)'
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
                    'container_id int',
                    'target_id int',
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
                    'id int primary key',
                    'intvalue int'
                ],
                'passthrus'=>[
                    'id int',
                ],
                'referenceonlies'=>[
                    'id int primary key',
                    'testint int'
                ],
                'secondlevelchildren'=>[
                    'id int primary key',
                    'childint int'                  
                ],
                'testchildren'=>[
                    'id int primary key',
                    'childint int',
                    'childchar varchar(100)',
                    'childfloat float',
                    'childtext text',
                    'childdatetime datetime',
                    'childdate date',
                    'childtime time',
                    'childenum enum ('testA','testB','testC')'
                ],
                'testparents'=>[
                    'id int primary key',
                    'parentint int',
                    'parentchar varchar(100)',
                    'parentfloat float',
                    'parenttext text',
                    'parentdatetime datetime',
                    'parentdate date',
                    'parenttime time',
                    'parentenum enum ('testA','testB','testC')'
                ],
                'thirdlevelchildren'=>[
                    'id int primary key',
                    'childchildint int'
                ],
                'dummies'=>[
                    'id int primary key',
                    'dummyint int'
                ]
      ];
    }
  
    protected function GetTables() {
      return [
           'object'=>[
                ['id','classname','created_at'],
                [
                    'dummy1'=>[1,'dummy','2019-05-15 10:00:00'],
                    'dummy2'=>[2,'dummy','2019-05-15 10:00:00'],
                    'dummy3'=>[3,'dummy','2019-05-15 10:00:00'],
                    'dummy4'=>[4,'dummy','2019-05-15 10:00:00'],
                    'parent'=>[5,'testparent','2019-05-15 10:00:00'],
                    'child'=>[6,'testchild','2019-05-15 10:00:00'],
                    'passthru'=>[7,'passthru','2019-05-15 10:00:00'],
                ]
           ],
           'objectobjectassigns'=>[
               ['container_id','element_id','field','index'],
               [
                   ['=>parent','=>dummy1','parentobject',0],
                   ['=>parent','=>dummy2','parentoarray',0],
                   ['=>parent','=>dummy3','parentoarray',1],
                   ['=>child','=>dummy3','parentobject',0],
                   ['=>child','=>dummy1','parentoarray',0],
                   ['=>child','=>dummy2','parentoarray',1],
                   ['=>child','=>dummy3','childobject',0],
                   ['=>child','=>dummy3','childoarray',0],
                   ['=>child','=>dummy4','childoarray',1],
                   ['=>child','=>dummy1','childoarray',2],
                ]
           ],
           'stringobjectassigns'=>[
               ['container_id','element_id','field','index'],
               [
                   ['=>parent','ObjectString0','parentsarray',0],
                   ['=>parent','ObjectString1','parentsarray',1],
                   ['=>child','Parent0','parentsarray',0],
                   ['=>child','Parent1','parentsarray',1],
                   ['=>child','Child0','childsarray',0],
                   ['=>child','Child1','childsarray',1],
                   ['=>child','Child2','childsarray',2],
                ]
           ],
           'tags'=>[
               ['id','name','parent_id','options'],
               [
                   [1,'TagA',0,0], // TagA
                   [2,'TagB',0,0], // TagB
                   [3,'TagC',2,0], // TagB.TagC
                   [4,'TagD',0,0], // TagD
                   [5,'TagE',0,0], // TagE
                   [6,'TagF',0,0], // TagF
                   [7,'TagG',6,0], // TagF.TagG
                   [8,'TagE',7,0], // TagF.TagG.TagE
               ]
           ],
           'tagobjectassigns'=>[
               ['container_id','tag_id'],
               [
                   ['=>dummy1','TagA'],
                   ['=>dummy1','TagB'],
               ]
           ],
           'tagcache'=>[
               ['id','name','tag_id'],
               [
                   [1,'TagA','=>TagA'],
                   [2,'TagB','=>TagB'],
                   [3,'TagC','=>TagC'],
                   [4,'TagB.TagC','=>TagC'],
                   [5,'TagD','=>TagD'],
                   [6,'TagE','=>TagE'],
                   [7,'TagF','=>TagF'],
                   [8,'TagG','=>TagG'],
                   [9,'TagF.TagG','=>TagG'],
                   [10,'TagE','=>TagE'],
                   [11,'TagG.TagE','=>TagE'],
                   [12,'TagF.TagG.TagE','=>TagE'],
                ]
           ],
          
      ];
    }
  
}
