INSERT INTO `dummies` (`id`,`dummyint`) VALUES ((1,123),(2,234),(3,345),(4,456));

INSERT INTO `tags` (`id`,`name`,`parent_id`,`options`) VALUES (
(1,`TagA`,0,0), 
(2,`TagB`,0,0), 
(3,`TagC`,2,0), 
(4,`TagD`,0,0), 
(5,`TagE`,0,0), 
(6,`TagF`,0,0), 
(7,`TagG`,6,0), 
(8,`TagE`,7,0)); 

INSERT INTO `tagcache` (`id`,`name`,`tag_id`) VALUES (
(1,`TagA`,1),
(2,`TagB`,2),
(3,`TagC`,3),
(4,`TagB.TagC`,3),
(5,`TagD`,4),
(6,`TagE`,5),
(7,`TagF`,6),
(8,`TagG`,7),
(9,`TagF.TagG`,7),
(10,`TagE`,8),
(11,`TagG.TagE`,8),
(12,`TagF.TagG.TagE`,8));

INSERT INTO `attributes` (`name`,`type`,`allowedobjects`,`property`) VALUES (
(`int_attribute`,`int`,`\\Sunhill\\ORM\\Tests\\Objects\\Dummy`,``),
(`attribute1`,`int`,`\\Sunhill\\ORM\\Tests\\Objects\\TestParent`,``),
(`attribute2`,`int`,`\\Sunhill\\ORM\\Tests\\Objects\\TestParent`,``),
(`general_attribute`,`int`,`\\Sunhill\\ORM\\Tests\\Objects\\ORMObject`,``),
(`char_attribute`,`char`,`\\Sunhill\\ORM\\Tests\\Objects\\Dummy`,``),
(`float_attribute`,`float`,`\\Sunhill\\ORM\\Tests\\Objects\\Dummy`,``),
(`text_attribute`,`text`,`\\Sunhill\\ORM\\Tests\\Objects\\Dummy`,``)
);

