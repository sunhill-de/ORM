# Internal details

## Database storage
The default storage is the database storage. It uses the laravel DB facade so it doesn't mind what datbase engine works in the background (mariadb, sqlite). The database is structured in this way

### Table objects
Every object of the ORM framework has to be stored in this table. The table gives fundamental informations (like class, UUID and access rights).

The table <b>objects</b> defines the following fields:
- id : int (auto increment) = The internal id of the object
- uuid : string = a internal created uuid of the object in the case the database has to exchange entries with other ORM implementations.
- obj_owner = An id of the user that owns this object (0 means nobody owns it)
- obj_group = An id of the group that owns this object (0 means no group owns it)
- obj_read = Who is allowed to read this object
- obj_edit = Who is allowed to edit this object
- obj_delete = Who is allowed to delete this object

Note: By default the obj_xxxx fields are set to very open values. Anybody can read, edit and delete the objects.

### Table tags
Tags are a way to add markers to an object. Tags are organized in a hirarchic way, so a tag can have a parent or child tag.  For more information see [Tags](/doc/md/TAGS.md)

The tags are stored in the table tags and this defines the following columns:
- id = an auto increment field of the id of this tag
- name = The name of this tag
- options = Options of this tag
- parent_id = The id of the parent tag or 0 if this tag has no parent

### Table tagcache
Due the reason that tags can be hirarchic there is this table that stores all combinations of the tag for searching purposes.
- id = internal id of this tagcache entry
- path_name = the name of the tag or one of this combination
- tag_id = The id of the tag this entry points to
- is_fullpath = Boolean field that indicates that this entry is the fullpath of a path (true) or just a part of it (false)
 
### Table tagobjectassigns
To store what tag is assiged to what object this table is used. (It's a simple n:m relation). It defines the following columns:
- container_id = The id of the object the following tag is assigned to
- tag_id = the id of the tag the object is assigned to

### Table attributes
Attributes are a way to add dynamic properties to objects. Dynamic means not every object of the given class has to define this property. This makes it possible to store additional information to certain objects. For more information see [Attributes](/doc/md/ATTRIBUTES.md)

This table stores the information about a certain attribute and defines the following columns:
- id = An auto incremented id of this attribute
- name = The name of this attribute
- allowedobjects = a comma seperated list of classes this attribute can assigned to
- type = The type of the attribute

### Table attributeobjectassigns
For searching purposes this is a help table that connects the attributes with the objects in form of a n:m table. This table defines the following columns:
- object_id: int = The id of the object
- attribute_id: int = The id of the attribute

### Table of attribute values
Every attribute has an own table that stores its attribute values. This table is named after the attribute (so if the attribute is called testvalue the table is also called testvalue. Note: no plural here). This table consist of two columns:
- object_id: int = The id of the object
- value = Depending of the type of the attribute 

### Tables of classes
Every class (no matter if they define own properties or not) defines an own table. The name of the table if defined in the <b>table</b> info block. For example the info block defines <b>testobject</b> as table name there is at least a table with this name. This table defines at least an id field of the type integer that defines the internal id of this object an is the same as in the objects table. All properties except arrays, maps, tags and attributes are stored in this table too. 

If the class defines an array or map there is an additional table with the tablename combined with an underscore and the name of the property (e.g. testobject_testarray). This helping table has following columns:
- id : int = the id that is the same as in the main table and the objects table
- value = dependig of the data type of the array or map this value is of the according type and stores the value of the array element
- index = in an array this is an numeric index, in a map this is a string that defines the index of the array/map

