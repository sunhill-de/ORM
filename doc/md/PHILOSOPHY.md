# Introduction into the core concept

## Motivation
The main motivation of creating the orm framework was, that I didn't like, that there is no inheritance in laravel's eloquent. You can have a table 'animal' and a table 'birds' but no direct relation in the form a bird->animal (a bird is an animal). The managing of this relation creates some overhead so for more simple tables there is a concept similar to eloquent called collections.

## Difference between a collection and an object
While an object provides the possibility to create a relation structures between classes (like a bird is an animal) a collection is just a plain old flat class to database mapper. You can create a collection like "bird" but it won't store any information that a bird is an animal. For simple data that needs no hirachy this is sufficient. 

## Difference between collections/objects and properties
Long story short collections and objects manage (and collect) different properties. A property is the smallest part of information (like integer, string) while the object collects different properties. Lets say we have a collection called "animal". This collection can have the properties "name", "weight", "owner" and so on. It is even possible to create a property of a collection or object. In the example of the animals you can add a property calles "father" and "mother" with the type of "animal". 

## Difference between collections/objects and storages
While the collections or objects store and manage the informations the storage takes care on how this information are made persistant. The typical storage is a database (although it could be a flat file). When a collection/object is told to store itself it creates a storage internally and passes its data to this storage. The storage in turn handles the moving of the data into the storage (e.g. database).

## Summary
* A property is a single piece of information. It takes care of validating the information. 
* A collection is an assemblage of properties or pieces of information. It takes care of accessing the properties. 
* A storage is a possibility to store collections into a persistant storage (like a database). It takes care of passing the data from the collections to the physical storage. 