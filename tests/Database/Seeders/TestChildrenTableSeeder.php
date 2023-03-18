<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildrenTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('testchildren')->truncate();
	    DB::table('testchildren')->insert([
	        [
	            'id'=>17,
	            'childint'=>777,
	            'childchar'=>'WWW',
	            'childfloat'=>1.23,
	            'childtext'=>'amet. Lorem ipsum dolo',
	            'childdatetime'=>'1978-06-05 11:45:00',
	            'childdate'=>'1978-06-05',
	            'childtime'=>'11:45:00',
	            'childenum'=>'testC'
	        ],
	        [
	            'id'=>18,
	            'childint'=>801,
	            'childchar'=>'DEF',
	            'childfloat'=>8,
	            'childtext'=>'no sea takimata sanctus',
	            'childdatetime'=>'1974-09-15 17:45:00',
	            'childdate'=>'1974-09-15',
	            'childtime'=>'17:45:00',
	            'childenum'=>'testB'
	        ],
	        [
	            'id'=>19,
	            'childint'=>900,
	            'childchar'=>'ZZZ',
	            'childfloat'=>9,
	            'childtext'=>'Qt vero eos et accusam',
	            'childdatetime'=>'1941-06-10 17:45:00',
	            'childdate'=>'1941-06-10',
	            'childtime'=>'17:45:00',
	            'childenum'=>'testC'
	        ],
	        [
	            'id'=>19,
	            'childint'=>666,
	            'childchar'=>'ZOO',
	            'childfloat'=>6.66,
	            'childtext'=>'sanctus est Lorem ipsum',
	            'childdatetime'=>'1944-08-08 10:45:00',
	            'childdate'=>'1944-08-08',
	            'childtime'=>'10:45:00',
	            'childenum'=>'testC'
	        ],
	        [
	            'id'=>21,
	            'childint'=>112,
	            'childchar'=>'DEF',
	            'childfloat'=>5.8,
	            'childtext'=>'clita kasd gubergren',
	            'childdatetime'=>'1922-09-15 00:00:00',
	            'childdate'=>'1922-09-15',
	            'childtime'=>'00:00:00',
	            'childenum'=>'testC'
	        ],
	        [
	            'id'=>22,
	            'childint'=>321,
	            'childchar'=>'WED',
	            'childfloat'=>4.32,
	            'childtext'=>'no sea takimata sanctus est Lorem',
	            'childdatetime'=>'1916-06-17 00:11:00',
	            'childdate'=>'1916-06-17',
	            'childtime'=>'00:11:00',
	            'childenum'=>'testB'
	        ],
	        [
	            'id'=>23,
	            'childint'=>345,
	            'childchar'=>'QWG',
	            'childfloat'=>3.45,
	            'childtext'=>'dolore magna aliquyam erat',
	            'childdatetime'=>'1900-01-01 00:00:00',
	            'childdate'=>'1900-01-01',
	            'childtime'=>'00:00:00',
	            'childenum'=>'testC'
	        ],
	        [
	            'id'=>24,
	            'childint'=>777,
	            'childchar'=>null,
	            'childfloat'=>7.23,
	            'childtext'=>'Qt vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren',
	            'childdatetime'=>'1999-12-31 23:59:59',
	            'childdate'=>'1999-12-31',
	            'childtime'=>'23:59:59',
	            'childenum'=>'testC'
	        ],
	    ]);
	}
}