# Queries

The ORM-frameworks defines queries so that all entities (like tags, classes, attributes and so on) can be accessed the same way. The structure is oriented to the laravel QueryBuilder with some minor additions for dealing with the single entities.

## Common query functions
As in QueryBuilder it is possible to build chains of request elements (like ->where('this','=','that')->orderBy('something')->offset(2)->limit(2)). 
### Finishing method
Normally there must be a finishing method. All queries should define at least:
#### count(): int
returns the count of entries that match a given condition

#### first()
Returns the first entry that match a given condition. This method should normally return a StdClass objects with the expcetion of object queries that return the appropriate object.

#### get(): Collection
Return all entries that match a given condition. The method returns an Illuminate\Support\Collection object. The entries of this collection are normally StdClasses with the exception of object queries that consist of the appropriate objects.

### Query building
These method always return a reference to the builder object so that chaining is possible. Normally at least the following methods should be defined
#### where()/whereNot()/orWhere()/orWhereNot()
The same method as the QueryBuilder of laravel provides. These methods can take up to three parameters (key, relation and value). If the last one is omitted it is assumed that the relation is "=" and the second parameter means the value (Example ->where('id',2) is the same as ->where('id','=',2)

#### offset(int $offset)
The same method as the QueryBuilder of laravel provides. The list starts with the $offet-th entry.

#### limit(int $limit)
The same method as the QueryBuilder of laravel provides. The list consists of $limit entries maximum.

#### order($key, $direction = 'asc')
The same method as the QueryBuilder of laravel provides. The list is ordered by $key in the direction $direction. $direction only may take 'asc' for ascending sort and 'desc' for descending. 

## Classes::query()
The [Classes facade](doc/md/CLASSES.md) defines a method ::query() that makes it possible to query for classes. 
### Element structure
The methods ->first() and ->get() return a StdClass object or an array of StdClass objects. Each of these StdClass objects holds the informations about one single class. These are:
- class = The fully qualified php classname including the namespace
- name = The (internal) name of the class.
- description = A short description of the purpose of this class
- parent = The internal name of the parent class. 
- properties = An array of all properties that this class defines.


