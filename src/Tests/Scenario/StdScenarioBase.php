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
           'objects'=>[
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
           'externalhooks'=>[
               ['id','container_id','target_id','action','subaction','hook','payload']
               [
                   [1,'=>dummy1','=>dummy2','PROPERTY_UPDATED','dummyint','dummyint_updated',null],
                   [2,'=>dummy2','=>dummy1','PROPERTY_UPDATED','dummyint','dummyint2_updated',null],
                   [3,'=>dummy1','=>parent','PROPERTY_UPDATED','dummyint','dummyint3_updated',null],
               ]
           ],
          'caching'=>[
              ['id','object_id','fieldname','value'],
              [
                  [1,"=>parent","parentcalc","123A"],
                  [2,"=>child","parentcalc","234A"]
              ]
          ],
          'attributes'=>[
              ['name','type','allowedobjects','property'],
              [
                  'int_attribute'=>['int_attribute','int',"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",''],
                  'attribute1'=>['attribute1','int',"\\Sunhill\\ORM\\Test\\TestParent",''],
	              'attribute2'=>['attribute2','int',"\\Sunhill\\ORM\\Test\\TestParent",''],
	              ['general_attribute','int',"\\Sunhill\\ORM\\Objects\\ORMObject",''],
	              ['char_attribute','char',"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",''],
	              ['float_attribute','float',"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",''],
	              ['text_attribute','text',"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",''],
              ]
          ],
          'attributevalues'=>[
              ['attribute_id','object_id','value','textvalue'],
              [
                  ['=>int_attribute','=>dummy1',111,''],
                  ['=>attribute1','=>parent',121,''],
                  ['=>attribute1','=>child',232,''],
                  ['=>attribute2','=>child',121,''],
              ]
         ],
         'passthrus'=>[
             ['id'],
             [
                 ["=>passthru']
             ]
         ],
         'testchildren'=>[
             ['id','childint','childchar','childfloat','childtext','childdatetime','childdate','childtime','childenum'],
             [
                ['=>child',345,'GHI',3.45,'Norem Torem','1973-01-24 18:00:00','2016-06-17','18:00:00','testA']
             ]
         ],
         'testparents'=>[
             ['id','parentint','parentchar','parentfloat','parenttext','parentdatetime','parentdate','parenttime','parentenum'],
             [
                 ['=>parent',123,'ABC',1.23,'Lorem ipsum','1974-09-15 17:45:00','1978-06-05','01:11:00','testC'],
                 ['=>child',234,'DEF',2.34,'Upsala Dupsala','1970-09-11 18:00:00','2013-11-24','16:00:00','testB'],
                 ['=>passthru',321,'FED',4.32,'Ups Dup','1970-09-11 18:00:00','2013-11-24','16:00:00','testB']
		     ]
         ]
      ];
    }
  
}
