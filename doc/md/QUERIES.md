# Queries

The ORM-frameworks defines queries so that all entities (like tags, classes, attributes and so on) can be accessed the same way. The structure is oriented to the laravel QueryBuilder with some minor additions for dealing with the single entities.

## Common query functions
As in QueryBuilder it is possible to build chains of request elements (like ->where('this','=','that')->orderBy('something')->offset(2)->limit(2)). 

### Finishing methods
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

### Additional methods
#### query()
This method makes it possible to use the result of a class query to do an object query

## Tags::query()
The [Tags facade](doc/md/TAGS.md) defines a method ::query() that makes it possible to query for tags.

### Element structure
The methods ->first() and ->get() return a StdClass object or an array of StdClass objects. Each of these StdClass objects holds the information about one single tag. These are:
- id = The id of the tag
- name = The name of the tag
- full_path = The complete name of the tag including its parents
- parent_id = The id of the parent class 

### Field/pseudo fields
The fields that can be used in where or orderBy clauses are
- name = The name of the tag
- fullpath = The name of the tag prepended by the name of the parent(s) (like ParentTag.ChildTag)
- id = The id of the tag
- parent_id = The id of the parent tag of 0 if there is none
only in where statement
- parent = Takes the name of the parent. Example: ```php query()->where('parent','TagA')->first()``` returns the first tag thats parent is TagA.


### Additional where statements
There are some additional where conditions for tag queries:
- is assigned = only tags that have objects assiged to
- not assigned = only  tags that don't have objects assigned to

### Additional method

#### delete()
Like in Laravel this deletes the set of tags that where selected before. If there is no where statement then the whole tags table is wiped.
So: 
```Tags::query()->delete()``` wipes everything
```Tags::query()->where('id',2)->delete()``` deletes the tag with id 2

#### update()
Like in Laravel this updates a Tag. The fields name, parent_id and options take the values directly while an update to the field parent takes the (unique!) name of a tag, searches it and assigns it to parent_id.
So 
```Tags::query()->where('id',2)->update('parent','TagC');``` makes TagC the parent of TagB.
```Tags::query()->where('id',2)->update('name','TagC');``` renames TagB to TagC.
Be careful
```Tags::query()->update('name','TagC');``` will rename ALL tags to TagC without warning.

#### getTags()
In addition to get() this method preloads the tags and return a collection of all tags that fit to the condition.

## Attributes::query()
The [Attributes facade](doc/md/ATTRIBUTES.md) defines a method ::query() that makes it possible to query for attributes.

### Element structure
The methods ->first() and ->get() return a StdClass object or an array of StdClass objects. Each of there StdClass objects hold the information about a single tag- These are:
- id = The id of the attribute
- name = The name of the attribute
- type = the type of the attribute. Allowed types are ('integer', 'string', 'float', 'date', 'datetime', 'time', 'text', 'object', 'collection')
- allowed_classes = A comma seperated list of all classes that allowed to use this attributes. If every class should be able to use it, assign allowed_classes to "object". 

### Fields/pseudo fields
