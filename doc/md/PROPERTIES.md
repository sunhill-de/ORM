
# Properties
A property is a like a member variable of an Collection or ORMObject. They are accessed like normal members and normally you don't have to worry about there internal details. The only thing is, that you have to define them in the static setupProperties() method. This method takes a PropertyList parameter. A PropertyList is just a helper class that provides some defining methods to make it easy to define the properties (in fact it uses an similar concept like migrations of laravel). Via this PropertyList parameter you define the properties of your class. All property defining methods (like integer(), string(), etc.) take a name of the property as a parameter.

## Names of properties
The rules for property names are the same as for php variables, any alphanumeric character including "_" can be used, the name must't start with a digit. However, there are a few more restrictions for naming:
- A name must't start with an underscore ("_") because these names are reserved internally.
- The name must not be **tags**, **attributes**, **id**, **classname** 

## Modifiers for all properties
While defining properties you can modifiy some parameters of the property. Modifier always return the property object so it is possible to build a modifier chain. These are the modifiers that are avaiable to all properties:

### readonly() / setReadonly()
These modifiers are synonym and mark this property as readonly. A attempt to give this property a new value will raise an exception.

### searchable() / setSearchable()
These modifiers are synonym and mark this properts as searchable. The attempt to search for a non-searchable property will raise an exception. See [Searching](/doc/searching.md) for more details about searching.

### unit() / setUnit()
Every property can have an unit. This is part of the semantics mechanism an explained in detail [there](doc/md/SEMANTIC.md)

### semantic() / setSemantic()

## Property types
### Integer
```
...
$list->integer('integer_property);
...
```
Adds an integer property to this collection/object. Only integer values or integer strings can be assigned to this property.

```php
$object->integer_property = 123;
$object->integer_property = '123';
```

Note: If you assign a non integer value to this property an exception will be raised. 
```php
$object->integer_property = 'ABC'; // raises InvalidValueException
```

### String
```
...
$list->string('string_property);
// or
$list->varchar('string_property);
...
```
Adds a string property to this collection/object. Any scalar value can be assigned to this property:
```php
$object->string_property = 1;
$object->string_property = 'ABC';
$object->string_property = 1.2;
```

Any non-scalar value will raise an InvalidValueException
```php
$object->string_property = $another_object; // raises InvalidValueException
```

Both methods take a second integer parameter that defines the maximal string length of this property
```
$list->string('string_property',10); // defines a string with maximum length of 10
```
Note: If you later assign a value to this property that is longer than 10 characters, no exception will be raised. The property truncates the given string to a maximum length of 10.


### Float
```php
...
$list->float('float_property');
...
```
Adds a float property to this collection/object. Any integer or float value can be assigned to this property:
```php
$object->float_property = 1;
$object->float_property = 2.3;
```

### Text
```php
...
$list->text('text_property');
...
```
Adds a text field property to the collection/object. A text of any length can be assigned to a text field property.
```php
$object->text_property = file_get_contents('a_very_large_text.txt');
``

### Date/Datetime/Time
```php
...
$list->date('date_property');
$list->datetime('datetime_property');
$list->time('time_property');
...
```
Adds a date/datetime/time property to the collection/object. At the moment only integer (Unix timestamps) or strings in the form of "YYYY-MM-DD HH:MM:SS" and their partial forms can be assigned.
```php
$object->date_property = '2023-06-14';
$object->date_property = '2023-6-2'; // is expanded to 2023-06-02
$object->date_property = 1686778521; // is converted to 2023-06-02
```

### Enum
```php
...
$list->enum('enum_property')->setEnumValues(['Dog','Cat','Mouse']);
...
```
Adds an enum property to the collection/object. 
#### setEnumValues
It's necessary to chain the setEnumValues method to tell the property which values are allowed. This method takes an array of strings. When a value other than defined in this array is assigned, an InvalidValueException is raised. 
```php
$object->enum_property = 'Dog'; // OK
$object->enum_property = 'Elephant'; // raises InvalidValueException
```

### Array
```php
...
$list->array('array_property')->setElementType(PropertyString::class);
...
```
Adds an array property to the collection/object. This property can be access like any regular array.
```php
$object->array_property = ['A','B','C'];
echo $object->array_property[1]; // Returns 'B'
$object->array_property[] = 'D'; // Adds 'D' to the end
echo count($object->array_property); // Returns 4
echo empty($object->array_property); // Returns false
```


#### setElementType
While defining an array you must hint the allowed element type. The method setElementType takes a string that have to be the class name of an Property (e.g. PropertyString::class). Any scalar property type like Integer, Varchar, Date, Time, Datetime, Boolean, Enum or Object is allowed. Array, Maps and external References are not allowed as an element type. 

### Map
```php
...
$list->map('map_property')->setElementType(PropertyString::class);
...
```
#### setElementType
While defining a map you must hint the allowed element type. The method setElementType takes a string that have to be the class name of an Property (e.g. PropertyString::class). Any scalar property type like Integer, Varchar, Date, Time, Datetime, Boolean, Enum is allowed. Array, Maps and external References are not allowed as an element type.

#### setMaxiumKeyLength
The keys of maps are always strings. The maxium length of the key is defaulted to 20. With this method the default value can be changed. 

### Object
```php
...
$list->object('object_property')->setAllowedClasses([Class1::class,Class2::class]);
...
```
Adds an object field to the collection/object. An object field is a reference to another object. The property can be accessed like any other object.
```php
$object->object_property = new Class1();
$object->object_property->name = 'Some name';
```

#### setAllowedClasses
Defines what kind of objects are allowed to be assigned to this field.

### Collection
```php
...
$list->collection('collection_property')->setAllowedClasses([Class1::class,Class2::class]);
...
```
Adds a collection field to the collection/object. An collection field is a reference to another collection. The property can be accessed like any other collection.
```php
...
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Collection;

class MyCollection extends Collection
{
    protected static function setupProperties(PropertyList $list)
    {
        $list->string('name');
    }
}

class MyObject extends ORMObject
{
    protected static function setupProperties(PropertyList $list)
    {
        $list->collection('object_property')->setAllowedClass(MyCollection::class);
    }
}

...
$object = new MyObject();
$object->object_property = new MyCollection();
$object->object_property->name = 'Some name';
...
```

#### setAllowedClass
Defines which collection is allowed to be assigned to this field. This method takes a string that is the name of the collection class that are allowed to be assigned this property.

### Keyfield
```php
...
$list->keyfield('keyfield_property')->setBuildRule(':field1 :field2');
```
A keyfield is a virtual field that makes it possible to combine one or more other fields to a new field

```php
$object->field1 = 'ABC';
$object->field2 = 'DEF';
echo $object->keyfield = 'ABC DEF';
```
#### setBuildRule()
Keyfields need a rule how they are built. This is done with this method. Every string starting with a colon and a series of alphanumeric characters is interpreted as a field of the owning collection. In the example above the buildRule references the two fields called field1 and field2. Keyfield rules can be more complex (like ":name (:year)") and can even refer to fields of object/collection fields. When an object defines the property 'object_field' that refers to another object than you can define a keyfield with "object_field->name". The keyfield then takes the name of the refered object. If the object field is empty an empty string will be replaced. 

### Calculated fields
```php
...
$list->calculated('calc_property')->setCallback(function($collection) {
	return md5($collection->name);
});
...
```
Calculated field offer the possibility to store automatically complex calculations in the storage. Calculated fields are read only by nature, any attempt to assign a value to them will raise an exception. Calculated values are stored as string in the storage and can be searched too. You should only use calculated fields when you need the searching capabilities or it's quite expensive to calculate this field. Calculated fields need a callback (see next paragraph).

### setCallback()
This method takes either a string or a closure. When a string is passed this string is assumed to be the name of a method of the owning collection that performs the calculation. This method then takes no parameter and has to return the calculated value. If the parameter is a closure, this function takes the calling propertiescollection as parameter and returns the calculated value the same way as the method.

### External reference
```php
...
$list->externalReference('external_property');
...
```
