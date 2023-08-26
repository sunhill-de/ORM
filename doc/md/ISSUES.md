# Issues

Known bugs
- [ ] Migration of collections/objects can't change the maximum string length because there is no way to reliable retrieve the current string length in the database engine

Missing features
- [ ] There is no possibility to promote an object to another object

Wish list
- [ ] It should be possible to combine queries of different entities (like dummy->search()->where('tag','contains',Tags::query()->where('name','begins with','A'))->get())
