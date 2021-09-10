# ORM
The basic ORM framework of the sunhill project provides a <b>O</b>bject-<b>R</b>elational-<b>M</b>anager extension for the laravel framework. In opposite to <b>Eloquent</b> the sunhill ORM project respects inheritance.
That means you can use a object hirarchy tree that is mapped to the database tables. So it is easy to search for objects that a descendands of a certain object. 

## Basic usage
To let an object make use of the sunhill ORM framework is has to be derrived by the oo_object class. This basic class defines the methods commit() and rollback() that stores any changes to the fields (called properties) persistant or undoes them respectivly. The properties have to be defined via the static setup_properties() method. They have to be any descendant of oo_property. After that they can be access as normal class members.

Example:
```php
class test extends oo_object {

   protected static function setup_properties() {
      self::integer('test_int')->set_default(2);
      self::varchar('test_string')->set_default('Bla');
   }
   
}

class extendedtest extends test {

   protected static function setup_properties() {
      self::integer('test_float');
   }
   
}

...

{
 $test = new extendedtest();
 $test->test_int = 3;
 $test->test_float = 3.3;
 $test->commit()
}
```
see also wiki.
