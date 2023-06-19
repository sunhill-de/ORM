# Attributes

With attributes it is possible to extend classes by additional fields. Another benifit is to share the same entity between totally differnt classes (like a rating). Due the more complex nature of attributes it's more efficient to put properties, that should be assigned to all members of a class into the class itself and not in an attribute.

## Usage
### Name and type
Attributes have to be creates before they can be used. To manage attributes there is the attribues facade. To define a new attribute it needs a name and a type. The naming schema is the same as for properties. If a class defines a property with the same name this attribute can't be assigned to an object of this class (because the orm system will always prefer the built-in property). The type can be any of the scalar property types like integer, string, boolean, enum, date, datetime, time or float. Arrays or maps can't be used at attributes. 

### Creation
To create an attribute for later usasge you can use the attributes query of the attributes facade:

```php
...
use Sunhill\ORM\Facades\Attributes;
...
Attributes::query()->insert(['name'=>'attribute_name','type'=>PropertyInteger::class]);
...
```
This creates an attribute with the name **attribute_name** of the type **integer**. To restrict the usage of this attribute to certain classes you can add one or more classes that are allowed for this attribute
```php
...
use Sunhill\ORM\Facades\Attributes;
...
Attributes::query()->insert(['name'=>'attribute_name','type'=>PropertyInteger::class,'allowed_classes'=>SomeClass::class]);
...
```

### Assigning to an object
If you want to assign the previous created attribute to an object just use it like any other property:
```php
$object = new SomeClass();

$object->attribute_name = 10;
```

The orm system searches for attributes whenever you try to assign a property that does not exist in the class. 

###
