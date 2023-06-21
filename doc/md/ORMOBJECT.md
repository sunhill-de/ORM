# ORMObject

To map a hirarchical class relation to a database system there is the class ORMObject. Every classes that make use of this mechanism should be derrived from ORMObject. If you just need plain class to database mapping without inheritance you should consider useing a [Collection](doc/md/COLLECTION.md).

Every class that is derrived from ORMObject has at least to define the static function setupInfos() that gives fundamental informations about the class. Normally a derrived class also defines the setupProperties() function that defines the properties of this class.

## Static methods
Collections and ORMObject share the same mechanism for defining properties and storing general class informations. For details see [Common static methods](doc/md/DEFINING.md).

## Dynamic method

### commit()

### rollback()


