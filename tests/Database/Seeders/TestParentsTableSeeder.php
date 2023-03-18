<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('testparents')->truncate();
	    DB::table('testparents')->insert([
		    [
		        'id'=>9,
		        'parentint'=>111,
		        'parentchar'=>'ABC',
		        'parentfloat'=>1.11,
		        'parenttext'=>'Lorem ipsum',
		        'parentdatetime'=>'1974-09-15 17:45:00',
		        'parentdate'=>'1974-09-15',
		        'parenttime'=>'17:45:00',
		        'parentenum'=>'testC'
		    ],
	        [
	            'id'=>10,
	            'parentint'=>123,
	            'parentchar'=>'DEF',
	            'parentfloat'=>1.23,
	            'parenttext'=>'consetetur sadipscing elitr',
	            'parentdatetime'=>'1970-09-15 18:45:00',
	            'parentdate'=>'1974-09-15',
	            'parenttime'=>'17:45:00',
	            'parentenum'=>'testB'
	        ],
	        [
	            'id'=>11,
	            'parentint'=>222,
	            'parentchar'=>'GHI',
	            'parentfloat'=>2.22,
	            'parenttext'=>'sed diam nonumy',
	            'parentdatetime'=>'1973-01-24 10:10:10',
	            'parentdate'=>'1973-01-24',
	            'parenttime'=>'10:10:10',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>12,
	            'parentint'=>123,
	            'parentchar'=>'EEE',
	            'parentfloat'=>1.23,
	            'parenttext'=>'eirmod tempor invidunt ut labore',
	            'parentdatetime'=>'2013-11-24 01:10:00',
	            'parentdate'=>'2013-11-24',
	            'parenttime'=>'01:10:00',
	            'parentenum'=>'testA'
	        ],
	        [
	            'id'=>13,
	            'parentint'=>234,
	            'parentchar'=>'DEF',
	            'parentfloat'=>2.34,
	            'parenttext'=>'Lorem ipsum dolor sit amet',
	            'parentdatetime'=>'2004-07-01 13:00:00',
	            'parentdate'=>'2004-07-01',
	            'parenttime'=>'13:00:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>14,
	            'parentint'=>555,
	            'parentchar'=>'TTT',
	            'parentfloat'=>5.55,
	            'parenttext'=>'dolor sit amet',
	            'parentdatetime'=>'2008-05-19 04:15:00',
	            'parentdate'=>'2008-05-19',
	            'parenttime'=>'04:15:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>15,
	            'parentint'=>432,
	            'parentchar'=>'XZT',
	            'parentfloat'=>4.32,
	            'parenttext'=>'sed diam voluptua. At vero',
	            'parentdatetime'=>'1974-09-15 17:45:00',
	            'parentdate'=>'1974-09-15',
	            'parenttime'=>'17:45:00',
	            'parentenum'=>'testB'
	        ],
	        [
	            'id'=>16,
	            'parentint'=>700,
	            'parentchar'=>null,
	            'parentfloat'=>7.0,
	            'parenttext'=>'consetetur sadipscing elitr',
	            'parentdatetime'=>'2004-07-01 17:45:00',
	            'parentdate'=>'2004-07-01',
	            'parenttime'=>'17:45:00',
	            'parentenum'=>'testC'
	        ],
	        
	        [
	            'id'=>17,
	            'parentint'=>123,
	            'parentchar'=>'RRR',
	            'parentfloat'=>1.23,
	            'parenttext'=>'amet. Lorem ipsum dolo',
	            'parentdatetime'=>'1978-06-05 11:45:00',
	            'parentdate'=>'1978-06-05',
	            'parenttime'=>'11:45:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>18,
	            'parentint'=>800,
	            'parentchar'=>'DEF',
	            'parentfloat'=>8,
	            'parenttext'=>'no sea takimata sanctus',
	            'parentdatetime'=>'1974-09-15 17:45:00',
	            'parentdate'=>'1974-09-15',
	            'parenttime'=>'17:45:00',
	            'parentenum'=>'testB'
	        ],
	        [
	            'id'=>19,
	            'parentint'=>900,
	            'parentchar'=>'ZZZ',
	            'parentfloat'=>9,
	            'parenttext'=>'At vero eos et accusam',
	            'parentdatetime'=>'1941-06-10 17:45:00',
	            'parentdate'=>'1941-06-10',
	            'parenttime'=>'17:45:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>20,
	            'parentint'=>666,
	            'parentchar'=>'ZOO',
	            'parentfloat'=>6.66,
	            'parenttext'=>'sanctus est Lorem ipsum',
	            'parentdatetime'=>'1944-08-08 10:45:00',
	            'parentdate'=>'1944-08-08',
	            'parenttime'=>'10:45:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>21,
	            'parentint'=>580,
	            'parentchar'=>'DEF',
	            'parentfloat'=>5.8,
	            'parenttext'=>'clita kasd gubergren',
	            'parentdatetime'=>'2022-09-15 00:00:00',
	            'parentdate'=>'2022-09-15',
	            'parenttime'=>'00:00:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>22,
	            'parentint'=>432,
	            'parentchar'=>'RED',
	            'parentfloat'=>4.32,
	            'parenttext'=>'no sea takimata sanctus est Lorem',
	            'parentdatetime'=>'2016-06-17 00:11:00',
	            'parentdate'=>'2016-06-17',
	            'parenttime'=>'00:11:00',
	            'parentenum'=>'testB'
	        ],
	        [
	            'id'=>23,
	            'parentint'=>345,
	            'parentchar'=>'ARG',
	            'parentfloat'=>3.45,
	            'parenttext'=>'dolore magna aliquyam erat',
	            'parentdatetime'=>'2000-01-01 00:00:00',
	            'parentdate'=>'2000-01-01',
	            'parenttime'=>'00:00:00',
	            'parentenum'=>'testC'
	        ],
	        [
	            'id'=>24,
	            'parentint'=>723,
	            'parentchar'=>null,
	            'parentfloat'=>7.23,
	            'parenttext'=>'At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren',
	            'parentdatetime'=>'1999-12-31 23:59:59',
	            'parentdate'=>'1999-12-31',
	            'parenttime'=>'23:59:59',
	            'parentenum'=>'testC'
	        ],
	        
	        [
	            'id'=>25,
	            'parentint'=>999,
	            'parentchar'=>'DEF',
	            'parentfloat'=>9.99,
	            'parenttext'=>'sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum',
	            'parentdatetime'=>'1974-09-15 17:45:00',
	            'parentdate'=>'1974-09-15',
	            'parenttime'=>'17:45:00',
	            'parentenum'=>'testC'
	            
	        ],
	        [
	            'id'=>26,
	            'parentint'=>123,
	            'parentchar'=>null,
	            'parentfloat'=>1.23,
	            'parenttext'=>'Lorem ipsum dolor sit amet, consetetur sadipscing',
	            'parentdatetime'=>'1999-12-31 23:59:59',
	            'parentdate'=>'1999-12-31',
	            'parenttime'=>'23:59:59',
	            'parentenum'=>'testB'
	        ],
	    ]);
	}
}