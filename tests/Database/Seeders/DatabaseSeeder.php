<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(ObjectsTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(TagCacheTableSeeder::class);
        $this->call(AttributesTableSeeder::class);
        $this->call(AttributeObjectAssignsTableSeeder::class);        
        $this->call(TagObjectAssignsTableSeeder::class);
        
        $this->call(AttrAttribute1TableSeeder::class);
        $this->call(AttrAttribute2TableSeeder::class);
        $this->call(AttrCharAttributeTableSeeder::class);
        $this->call(AttrChildAttributeTableSeeder::class);
        $this->call(AttrFloatAttributeTableSeeder::class);
        $this->call(AttrGeneralAttributeTableSeeder::class);
        $this->call(AttrIntAttributeTableSeeder::class);
        $this->call(AttrTextAttributeTableSeeder::class);        
        
        $this->call(DummiesTableSeeder::class);
        $this->call(DummychildrenTableSeeder::class);
        $this->call(TestParentsTableSeeder::class);
        $this->call(TestParentParentOArrayTableSeeder::class);
        $this->call(TestParentParentSArrayTableSeeder::class);
        $this->call(TestChildChildSArrayTableSeeder::class);
        $this->call(TestChildChildOArrayTableSeeder::class);
        
        $this->call(TestChildrenTableSeeder::class);
        $this->call(TestSimpleChildrenTableSeeder::class);
        $this->call(ReferenceOnliesTableSeeder::class);
        $this->call(ReferenceOnliesTestOArrayTableSeeder::class);
        $this->call(ReferenceOnliesTestSArrayTableSeeder::class);
        
        $this->call(SecondLevelChildrenTableSeeder::class);
        $this->call(CalcClassSeeder::class);

        $this->call(DummyCollectionsTableSeeder::class);
        $this->call(ComplexCollectionsTableSeeder::class);
        $this->call(ComplexCollectionsFieldOArrayTableSeeder::class);
        $this->call(ComplexCollectionsFieldSArrayTableSeeder::class);
        $this->call(ComplexCollectionsFieldSMapTableSeeder::class);
        
        $this->call(ExternalTableSeeder::class);
    }
}
