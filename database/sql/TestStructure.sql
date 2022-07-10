CREATE TABLE `dummies` (
  `id` int(10) UNSIGNED NOT NULL,
  `dummyint` int(10) UNSIGNED NOT NULL
);

CREATE TABLE `testparents` (
  `id` int(10) UNSIGNED NOT NULL,
  `parentint` int(10),
  `parentchar` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parentfloat` float,
  `parenttext` text,
  `parentdatetime` datetime,
  `parentdate` date,
  `parenttime` time,
  `parentenum` enum ('testA','testB','testC')
);

CREATE TABLE `testchildren` (
  `id` int(10) UNSIGNED NOT NULL,
  `childint` int(10),
  `childchar` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `childfloat` float,
  `childtext` text,
  `childdatetime` datetime,
  `childdate` date,
  `childtime` time,
  `childenum` enum ('testA','testB','testC')
);

CREATE TABLE `thirdlevelchildren` (
  `id` int(10) UNSIGNED NOT NULL,
  `childchildint` int(10)
);

CREATE TABLE `secondlevelchildren` (
  `id` int(10) UNSIGNED NOT NULL,
  `childint` int(10)
);

CREATE TABLE `referenceonlies` (
  `id` int(10) UNSIGNED NOT NULL,
  `testint` int(10)
);

CREATE TABLE `passthrus` (
  `id` int(10) UNSIGNED NOT NULL
);

CREATE TABLE `objectunits` (
  `id` int(10) UNSIGNED NOT NULL,
  `intvalue` int(10)
);

CREATE TABLE `searchtestA` (
  `id` int(10) UNSIGNED NOT NULL,
  `Aint` int(10),
  `Anosearch` int(10),
  `Achar` varchar(20)
);

CREATE TABLE `searchtestB` (
  `id` int(10) UNSIGNED NOT NULL,
  `Bint` int(10),
  `Bchar` varchar(20)
 );

CREATE TABLE `searchtestC` (
  `id` int(10) UNSIGNED NOT NULL
);
