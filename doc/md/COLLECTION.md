# Collections

Due the overhead a hirarchic object mapping creates, there are collections. Collection are a simple kind of object relational mapping (just like Eloquent) and doesn't support inheritance. Thus a collection class can't have descendants and is final from the database point of vier. Although you can derrive a class from a collection this new class is totally treated like a single collection without a parent class. Normally it shouldn't be necessary to derrive classes from existing collections.

Every class that is derrived from Collection has at least to define the static function setupInfos() that gives fundamental informations about the class. Normally a derrived class also defines the setupProperties() function that defines the properties of this class.

## Static methods
Collections and ORMObject share the same mechanism for defining properties and storing general class informations. For details see [Common static methods](doc/md/DEFINING.md).
