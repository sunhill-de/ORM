# ORM {#mainpage}
The basic ORM framework of the sunhill project provides a <b>O</b>bject-<b>R</b>elational-<b>M</b>anager extension for the laravel framework. In opposite to <b>Eloquent</b> the sunhill ORM project respects inheritance.
That means you can use a object hirarchy tree that is mapped to the database tables. So it is easy to search for objects that a descendands of a certain object. 

## Main difference to Eloquent
While Eloquent stores objects in a flat table there is now way to handle dependencies of objects. For example let there be two classes: The ancestor is the class 'Person' the descendant is the class 'Friend'. Every friend is a person but not every person is a friend. So there is no need to store the address to persons but it's useful for friends. Now it's easy to search for all friends with the name 'Smith' (like Friend::search()->where('lastname','Smith') ) and you can do the same for persons (Person::search()->where('lastname','Smith')) In the first example only friends with that name are returned in the second example all persons (including friends) with the name 'Smith' are returned. In Eloquent you can have a model Friend and a model Person but they are not related to each other. 

## Installation
Use composer to install sunhill/orm and its dependecies
```
composer require sunhill-de/orm
```

## Basic usage
To let an object make use of the sunhill ORM framework is has to be derrived by the ORMObject class. This basic class defines the methods commit() and rollback() that stores any changes to the fields (called properties) persistant or undoes them respectivly. The properties have to be defined via the static setup_properties() method. They have to be any descendant of Property. After that they can be access as normal class members.

Example:
```php
class test extends ORMObject {

    protected static function setupProperties(PropertyList $list)
    {
		$list->integer('dummyint')->searchable()->default(1);
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'test');
	    static::addInfo('table', 'tests');
	    static::addInfo('description', 'A demonstration class');
	}
   
}

class extendedtest extends test {

    protected static function setupProperties(PropertyList $list)
    {
		$list->varchar('dummystring')->searchable()->default(1);
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'extenedtest');
	    static::addInfo('table', 'extendedtests');
	    static::addInfo('description', 'A derrived demonstration class');
	}
   
}

...

{
 $test = new extendedtest();
 $test->dummystring = 'ABC';
 $test->dummyint = 3;
 $test->commit(); // writes the object to database (or storage)
 $id = $test->getID(); // Gives the numeric ID of this object
 echo $id;
 ...
 $test = Objects::load($id);
 echo $test->dummystring; // writes ABC
 echo $test->dummyint; // writes 3
 $test->dummystring = 'DEF';
 echo $test->dummystring; // writes DEF
 $test->commit(); // Writes the changes to database (or storage)
 
 $anothertest = Objects::load($id);
 echo $anothertest->dummystring; // writes DEF
 echo $test->dummystring; // writed DEF
}

```

## Registration of classes
Classes should be registered to the class manager to gain access to them. Every class must have an unique name (there are no namespaces yet) that is defined in the setupInfo() method. With this name the classes are access inside the orm system. For the system (in this case the class manager) to find these classes, they have to be registerd via the Classes::registerClass() method. If you use Laravel this registration is best put into a ServiceProvider boot() method.
```php
...
use Sunhill\ORM\Facades\Classes;
...

class SomeServiceProvider extends ServiceProvider
{
...
    public function boot()
    {
		...
		Classes::registerClass(test::class);
		Classes::registerClass(externdedtest::class);
		...
	 }
...
}	 
```

## Core concepts
### Properties
Every object consists of properties. There are simple properties like strings, integer and so on. There are array and maps of simple properties (there are no array of arrays yet) and there are objects and collections. Properties are defined in the static setupProperties() method of a class. A class can leave this method out so this class won't define own properties. This method takes a PropertyList object as an parameter, all definitions have to be made to this parameter
```php
...
class testclass extends ORMObject 
{
	protected static function setupProperties(PropertyList $list)
	{
		$list->integer('integer_field');
	}
}
...

```
The properties are later accessed like normal class members (e.g. "$test->integer_field"). There is an internal validation of the assigned values (so $test->integer_field = "ABC" will raise an exception). Array properties are accessed like normal array members ($test->array_field[1]). There is the property type "object" that takes other ORMObjects as values. These are accessed like this: $test->object_field->some_property. For more information see [Properties](doc/md/PROPERTIES.md)

### Collection and ORMObject
While the core component is a ORMObject there is a more simple form called Collection. The difference is explained here:

#### Collection
A collection is a simple flat object to database mapping like Eloquent. There is no hirarchy on the other hand there is less overhead while dealing with collections. 
See [Collection](doc/md/COLLECTION.md)

#### ORMObject
The ORMObject was (and is) the main motivation to write this package. It provides an easy way to map a hirarchic class structure to an database.
See [Collection](doc/md/ORMOBJECT.md)

### Classes and Objects
As usual there is a difference between a class and an object. A class is the blueprint for objects while objects are a concrete implementation of a class. Therefore there a two seperate facades for accessing informations about classes (called <b>Classes</b>, see ClassManager) and objects (called <b>Objects</b> see ObjectManager).

### Storage
The main intension for writing the sunhill ORM package was to store objects into a database. But the concept of storages adds the possibility to store objects whereever you want to (file, database, website). To accomplish that there is the concept of storages. Collections and ORMObjects manage the properties and the concept of dirtyness while a storage handles to reading, creating, modifying and erasing of the data to and from the concrete storage. Objects communicate via the <b>Storage</b> facade (see StorageManager) with the desired storage. For more informations see [Storage](doc/md/STORAGE.md)

### Tags
Tags are strings that can be attached to an Object. Tags are managed in a hirarchic way, so that a tag can have several child tags. The managing of tags are provided by the <b>Tags</b> facade (see TagManager). For more informations see [Tags](doc/md/TAGS.md)

### Attributes
Attributes are dynamic properties that can be assigned later to an object. That means a certain object can have this attribute but it doesn't has to. Attributes can be accesed (for reading and writing) as normal properties or member variables. Internally the ORM framework looks for a appropriate pre-defined attribute with this name and checks if this attribute is allowed for this class. If yes, the property is dynamically added to the object and can be accessed like any other property (and is stored in the storage and loaded back like any other property). For more information see [Attributes](doc/md/ATTRIBUTES.md) and the <b>Attributes</b> facade (see AttributeManager).

### Other utilities
#### Checks
The ORM package defines a artisan console command called 'sunhill:check' that executes a number of pre-defined checks. A check is a simple consistency probe for a single entity (like a database structure). With the execution of ./artisan sunhill:check all those checks are executed and return if there is a problem with any of the entities. The command has also the parameter --repair that tries to repair the found problem in the same run. For more informations see [Checks](doc/md/CHECKS.md).

#### Migrations
The ORM package defines an artisan console command called 'sunhill:migrate' that creates the storage structurs (like database tables) for every defined class. For more informations see [Migrations](doc/md/MIGRATIONS.md).

#### Queries
All entitis of the ORM framework (like attributes, tags, classes, objects) should be able to be queried in a form laravel performs a database query (via QueryBuilder). Therefore all facaces define a query() method that returns a query class to be used in a QueryBuilder like chain. For more informations see [Queries](doc/md/QUERIES.md)

## See also
+[Developer Resources](doc/md/INTERNAL.md)
+[Known issues](doc/md/ISSUES.md)