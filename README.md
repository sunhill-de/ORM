# ORM
The basic ORM framework of the sunhill project provides a <b>O</b>bject-<b>R</b>elational-<b>M</b>anager extension for the laravel framework. In opposite to <b>Eloquent</b> the sunhill ORM project respects inheritance.
That means you can use a object hirarchy tree that is mapped to the database tables. So it is easy to search for objects that a descendands of a certain object. 

## Main difference to Eloquent
While Eloquent stores objects in a flat table there is now way to handle dependencies of objects. For example let there be two classes: The ancestor is the class 'Person' the descendant is the class 'Friend'. Every friend is a person but not every person is a friend. So there is no need to store the address to persons but it's useful for friends. Now it's easy to search for all friends with the name 'Smith' (like Friend::search()->where('lastname','Smith') ) and you can do the same for persons (Person::search()->where('lastname','Smith')) In the first example only friends with that name are returned in the second example all persons (including friends) with the name 'Smith' are returned. In Eloquent you can have a model Friend and a model Person but they are not related to each other. 

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
	    static::addInfo('name_s', 'test');
	    static::addInfo('name_p', 'tests');
	    static::addInfo('description', 'A demonstration class');
	    static::addInfo('options', 0);
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
	    static::addInfo('name_s', 'extendedtest');
	    static::addInfo('name_p', 'extendedtests');
	    static::addInfo('description', 'A derrived demonstration class');
	    static::addInfo('options', 0);
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
The properties are later accessed like normal class members (e.g. "$test->integer_field"). There is an internal validation of the assigned values (so $test->integer_field = "ABC" will raise an exception). Array properties are accessed like normal array members ($test->array_field[1]). There is the property type "object" that takes other ORMObjects as values. These are accessed like this: $test->object_field->some_property. For more information see [Properties](doc/properties.md)

### Collection and ORMObject
While the core component is a ORMObject there is a more simple form called Collection. The difference is explained here:

#### Collection
A collection is a simple flat object to database mapping like Eloquent. There is no hirarchy on the other hand there is less overhead while dealing with collections. 

#### ORMObject
The ORMObject was (and is) the main motivation to write this package. It provides an easy way to map a hirarchic class structure to an database.

### Storage

### Tags

### Attributes

## See also
[Internal details](doc/internal.md)

### Database
see also wiki.
