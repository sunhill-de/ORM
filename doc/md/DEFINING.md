# Common static methods for Collections and ORMObjects

Due the fact that both classes have internally the same ancestor both define the same static methods that have to be overwritten in derrived classes.

## Static methods
### setupInfos()
It is possible to assign informations to a class. This is done in the setupInfos() method via the static addInfo() method. The first parameter of this method is the key of the information the second paremeter is the value of the information. A class can define any information it likes but at least these have to or should be defined:

- name = The name of this class. The name is used internally for indentification of the class and should not be confused with the php class name 
- table = The id for the storage (usually the name of the class main table)
- description = A short description what this class class and what's its purpose	    	    

You can can add more informations as you like.

## addInfo(string $key, $value)
To set a single key, use this method. It assigns the info identified by $key with the value.

## getInfo(string $key, $default)
To retrieve the value that was set with setInfo() earlier use getInfo. The parameter $key is the same that was uses in setInfo. If the key wasn't defined earlier the method checks if there is a default value. If yes, return this value otherwise raise an exception.

Example for setupInfos/getInfo/setInfo:
```php
class test extends ORMClass
{
	protected static setupInfos()
	{
	    static::addInfo('name', 'test');
	    static::addInfo('table', 'tests');
	    static::addInfo('description', 'A demonstration class.');
		static::addInfo('foo', 'bar');
	}
	
}

echo test::getInfo('name'); // returns 'test'
echo test::getInfo('foo');  // returns 'bar';
echo test::getInfo('notexisting','default'); // returns 'default'
echo test::getInfo('notexisting'); // raises an exception

```

### setupProperties(PropertyList $list)
