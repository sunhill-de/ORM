<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Storage\Mysql\WhereParser;
use Sunhill\ORM\Query\NotAllowedRelationException;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Query\TooManyWhereParametersException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Query\WrongTypeException;

class WhereParserTest extends TestCase
{
    
    /**
     * @dataProvider InputProvider
     * @param unknown $connection
     * @param unknown $key
     * @param unknown $relation
     * @param unknown $value
     * @param unknown $expect
     */
    public function testInput($class, $connection, $key, $relation, $value, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        $attribute = new \StdClass();
        $attribute->name = 'int_attribute';
        $attribute->type = 'integer';
        $avaiable_attributes = collect([$attribute]);
        Attributes::shouldReceive('getAvaiableAttributesForClass')->with(Dummy::class)->andReturn($avaiable_attributes);
        
        if (isset($expect[2]) && ($expect[2] == '*attribute*')) {
            $expect[2] = $attribute;
        }
        $test = new WhereParser();
        $test->setClass($class)->setConnection($connection)->setKey($key)->setRelation($relation)->setValue($value);
        if (is_string($expect)) {
            $this->expectException($expect);
        }
        $this->assertEquals($expect, $test->parseWhere());
    }
    
    public static function InputProvider()
    {
        return [
            [Dummy::class, 'where', 'dummyint', '=', 2,['handleWhereSimple','where', 'dummyint','=',2]],             // Trivial test
            [Dummy::class, 'whereNot', 'dummyint', '=', 2,['handleWhereSimple','whereNot', 'dummyint','=',2]],       // Trivial test
            [Dummy::class, 'orWhere', 'dummyint', '=', 2,['handleWhereSimple','orWhere', 'dummyint','=',2]],         // Trivial test
            [Dummy::class, 'orWhereNot', 'dummyint', '=', 2,['handleWhereSimple','orWhereNot', 'dummyint','=',2]],   // Trivial test

            [Dummy::class, 'where', 'dummyint', '=', null,['handleWhereSimple', 'where', 'dummyint', '=', null]],
            
            [Dummy::class, 'where', 'dummyint', 2, null,['handleWhereSimple','where', 'dummyint','=',2]],      // Test if default relation is =
            [Dummy::class, 'where', 'dummyint', '<', 2, ['handleWhereSimple','where','dummyint','<',2]],            // Test standard relations for scalars             
            [Dummy::class, 'where', 'dummyint', '<=', 2, ['handleWhereSimple','where','dummyint','<=',2]],          // Test standard relations for scalars
            [Dummy::class, 'where', 'dummyint', '>=', 2, ['handleWhereSimple','where','dummyint','>=',2]],          // Test standard relations for scalars
            [Dummy::class, 'where', 'dummyint', '>', 2, ['handleWhereSimple','where','dummyint','>',2]],            // Test standard relations for scalars
            [Dummy::class, 'where', 'dummyint', '==', 2, ['handleWhereSimple','where','dummyint','=',2]],           // Test standard relations for scalars
            [Dummy::class, 'where', 'dummyint', '<>', 2, ['handleWhereSimple','where','dummyint','<>',2]],          // Test standard relations for scalars
            [Dummy::class, 'where', 'dummyint', '!=', 2, ['handleWhereSimple','where','dummyint','<>',2]],          // Test standard relations for scalars
            
            [Dummy::class, 'where', 'dummyint', 'in', [2,3], ['handleWhereIn','where','dummyint',[2,3]]],          // Test standard relations for scalars
            [Dummy::class, 'where', 'dummyint', 'in', 2, ['handleWhereIn','where','dummyint',[2]]],          // Test standard relations for scalars
            
            [Dummy::class, 'where', 'dummyint', 'null', null, ['handleWhereNull', 'where', 'dummyint']],
            
            // Test unary conditions
            [Dummy::class, 'where', 'has associations', null, null, ['handleWhereHasAssociations', 'where']],
            [Dummy::class, 'where', 'is associated', null, null, ['handleWhereIsAssociated', 'where']],
            [Dummy::class, 'where', 'has tags', null, null, ['handleWhereHasTags', 'where']],
            [Dummy::class, 'where', 'has attributes', null, null, ['handleWhereHasAttributes', 'where']],

            // Special case for booleans
            [TestParent::class, 'where', 'parentbool', null, null, ['handleWhereBoolean', 'where', 'parentbool', '=', true]],
            
            // String handling
            [TestParent::class, 'where', 'parentchar', 'contains', 'A', ['handleWhereLike', 'where', 'parentchar', '%A%']],
            [TestParent::class, 'where', 'parentchar', 'begins with', 'A', ['handleWhereLike', 'where', 'parentchar', 'A%']],
            [TestParent::class, 'where', 'parentchar', 'ends with', 'A', ['handleWhereLike', 'where', 'parentchar', '%A']],
            
            // Array handling
            [TestParent::class, 'where', 'parentsarray', 'all of', ['A'], ['handleWhereAllOf', 'where', 'parentsarray', ['A']]],
            [TestParent::class, 'where', 'parentsarray', 'any of', ['A'], ['handleWhereAnyOf', 'where', 'parentsarray', ['A']]],
            [TestParent::class, 'where', 'parentsarray', 'has', ['A'], ['handleWhereAnyOf', 'where', 'parentsarray', ['A']]],
            [TestParent::class, 'where', 'parentsarray', 'none of', ['A'], ['handleWhereNoneOf', 'where', 'parentsarray', ['A']]],
            [TestParent::class, 'where', 'parentsarray', 'all of', 'A', ['handleWhereAllOf', 'where', 'parentsarray', ['A']]],
            [TestParent::class, 'where', 'parentsarray', '=', ['A','B'], ['handleWhereArrayEquals', 'where', 'parentsarray', ['A','B']]],
            [TestParent::class, 'where', 'parentsarray', '<>', ['A','B'], ['handleWhereArrayEquals', 'whereNot', 'parentsarray', ['A','B']]],
            
            [TestParent::class, 'where', 'parentmap', 'all keys of', ['A'], ['handleWhereAllKeysOf', 'where', 'parentmap', ['A']]],
            [TestParent::class, 'where', 'parentmap', 'any key of', ['A'], ['handleWhereAnyKeyOf', 'where', 'parentmap', ['A']]],
            [TestParent::class, 'where', 'parentmap', 'none key of', ['A'], ['handleWhereNoneKeyOf', 'where', 'parentmap', ['A']]],
            
            // Attribute handling
            [Dummy::class, 'where', 'int_attribute', '=', 3, ['handleWhereAttributeSimple','where','*attribute*','=',3]],
            [Dummy::class, 'where', 'int_attribute', 3, null, ['handleWhereAttributeSimple','where','*attribute*','=',3]],
            
            // Test exceptions
            [Dummy::class, 'where', 'dummyint', '?=', 2,NotAllowedRelationException::class],             // Unknown relation
            [Dummy::class, 'where', 'dummyint', 'contains', 2,NotAllowedRelationException::class],       // Relation not allowed with this field
            [Dummy::class, 'where', 'dummyint', 'all of', [2],NotAllowedRelationException::class],       // Relation not allowed with this field
            [TestParent::class, 'where', 'parentsarray', '<', 'A', NotAllowedRelationException::class],
            [Dummy::class, 'where', 'nonexisting', '=', 2,UnknownFieldException::class],                 // Field not known
            [Dummy::class, 'where', 'has associations', 'A', null, TooManyWhereParametersException::class],
            [Dummy::class, 'where', 'int_attribute', 'all of', 3, NotAllowedRelationException::class],
            [Dummy::class, 'where', 'int_attribute', 'in', 3, WrongTypeException::class],
            [TestParent::class, 'where', 'parentobject', '=', 'AB', WrongTypeException::class],
        ];
    }
}