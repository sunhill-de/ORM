
# Properties
A property is a like a member variable of an Collection or ORMObject. They are accessed like normal members and normally you don't have to worry about there internal details. The only thing is, that you have to define them in the static setupProperties() method. This method takes a PropertyList parameter. A PropertyList is just a helper class that provides some defining methods to make it easy to define the properties (in fact it uses an similar concept like migrations of laravel). Via this PropertyList parameter you define the properties of your class. All property defining methods (like integer(), string(), etc.) take a name of the property as a parameter.

## Modifiers for all properties
While defining properties you can modifiy some parameters of the property. Modifier always return the property object so it is possible to build a modifier chain. These are the modifiers that are avaiable to all properties:

### readonly() / setReadonly()
These modifiers are synonym and mark this property as readonly. A attempt to give this property a new value will raise an exception.

### searchable() / setSearchable()
These modifiers are synonym and mark this properts as searchable. The attempt to search for a non-searchable property will raise an exception. See [Searching](doc/searching.md) for more details about searching.

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
Note: If you assign a non integer value to this property an exception will be raised. 

### String
```
...
$list->string('string_property);
// or
$list->varchar('string_property);
...
```
Both methods take a second integer parameter that defines the maximal string length of this property
```
$list->string('string_property',10); // defines a string with maximum length of 10
```
Note: If you later assign a value to this property that is longer than 10 characters, no exception will be raises. The property truncates the given string to a maximum length of 10.
Note: You can assign a numeric value to a string property. It will be converted to a string automatically ($object->string_property = 1;)

