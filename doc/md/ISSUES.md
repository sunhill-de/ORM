# Issues

## Known bugs
- [ ] (#1) Migration of collections/objects can't change the maximum string length because there is no way to reliable retrieve the current string length in the database engine
- [X] (#17) When a property is named "name" it fails because it overwrites the protected property name
- [X] (#18) With array of collection an exception is raised due a unknown method
- [ ] (#19) An exception is raised when two both modified/created propertiescollecion with a circular dependency is committed (Hint: due setting the state to committing and locking further commits)
- [X] (#20) Stack overflow when commiting circular dependencies
- [X] (#21) Ordering of combined queries failes due ambigious id in order clause
- [X] (#23) Using id in where clause fails 
- [X] (#24) Double migration of objects migrates field of ancestors

## Missing features
### objects
- [X] (#2) There is no possibility to promote an object to another object
- [ ] (#22) It's not possible to modify the type of an attribute

### queries
- [X] (#3) Nested subqueries don't work reliable
- [X] (#4) Classes query with ->whereHasPropertyOfType()
- [X] (#5) Classes query with ->whereHasPropertyOfName()
- [X] (#6) Classes query with ->whereHasParent()
- [X] (#7) Classes query with ->whereIsParentOf()
- [ ] (#15) Classes query with ->query()

### properties
- [X] (#8) External references are not working
- [ ] (#9) Infofields are not working
- [X] (#16) Arrays of collections cant set the allowed collection type

### InfoMarket
- [X] (#10) InfoMarket not integtrated
- [X] (#11) Infomarket has no way to get the current offer
- [ ] (#12) It's not possible to get more results than one
- [ ] (#13) It's not possible to get a list of wanted results
      
## Wish list
- [ ] (#14) It should be possible to combine queries of different entities (like dummy->search()->where('tag','contains',Tags::query()->where('name','begins with','A'))->get())
