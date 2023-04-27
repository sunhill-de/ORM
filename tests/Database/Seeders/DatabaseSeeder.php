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
        $this->call(AttributeValuesTableSeeder::class);
        $this->call(TagObjectAssignsTableSeeder::class);
        
        $this->call(DummiesTableSeeder::class);
        $this->call(DummychildrenTableSeeder::class);
        $this->call(TestParentsTableSeeder::class);
        $this->call(TestParentArrayParentOArrayTableSeeder::class);
        $this->call(TestParentArrayParentSArrayTableSeeder::class);
        $this->call(TestChildArrayChildSArrayTableSeeder::class);
        $this->call(TestChildArrayChildOArrayTableSeeder::class);
        $this->call(TestChildCalcChildCalcTableSeeder::class);
        $this->call(TestParentCalcParentCalcTableSeeder::class);
        
        $this->call(TestChildrenTableSeeder::class);
        $this->call(TestSimpleChildrenTableSeeder::class);
        $this->call(ReferenceOnliesTableSeeder::class);
        $this->call(ReferenceOnliesArrayTestOArrayTableSeeder::class);
        $this->call(ReferenceOnliesArrayTestSArrayTableSeeder::class);
        
        $this->call(SecondLevelChildrenTableSeeder::class);
        $this->call(CalcClassSeeder::class);
        $this->call(CalcclassesCalcCalcfieldTableSeeder::class);
        $this->call(CalcclassesCalcCalcfield2TableSeeder::class);
    }
}
