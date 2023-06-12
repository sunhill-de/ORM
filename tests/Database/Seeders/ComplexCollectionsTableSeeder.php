<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * If I should sometimes wonder, why id doesn't start with 1 the data was copied from testparents! 
 * Makes it easier to maintain
 * @author klaus
 *
 */
class ComplexCollectionsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('complexcollections')->truncate();
	    DB::table('complexcollections')->insert([
		    [
		        'id'=>9,
		        'field_int'=>111,
		        'field_char'=>'ABC',
		        'field_float'=>1.11,
		        'field_text'=>'Lorem ipsum',
		        'field_datetime'=>'1974-09-15 17:45:00',
		        'field_date'=>'1974-09-15',
		        'field_time'=>'17:45:00',
		        'field_enum'=>'testC',
		        'field_object'=>1,
		        'field_calc'=>'111A'
		    ],
	        [
	            'id'=>10,
	            'field_int'=>123,
	            'field_char'=>'DEF',
	            'field_float'=>1.23,
	            'field_text'=>'consetetur sadipscing elitr',
	            'field_datetime'=>'1970-09-15 18:45:00',
	            'field_date'=>'1974-09-15',
	            'field_time'=>'17:45:00',
	            'field_enum'=>'testB',
		        'field_object'=>4,
	            'field_calc'=>'123A'
	        ],
	        [
	            'id'=>11,
	            'field_int'=>222,
	            'field_char'=>'GHI',
	            'field_float'=>2.22,
	            'field_text'=>'sed diam nonumy',
	            'field_datetime'=>'1973-01-24 10:10:10',
	            'field_date'=>'1973-01-24',
	            'field_time'=>'10:10:10',
	            'field_enum'=>'testC',
	            'field_object'=>5,
	            'field_calc'=>'222A'
	        ],
	        [
	            'id'=>12,
	            'field_int'=>123,
	            'field_char'=>'EEE',
	            'field_float'=>1.23,
	            'field_text'=>'eirmod tempor invidunt ut labore',
	            'field_datetime'=>'2013-11-24 01:10:00',
	            'field_date'=>'2013-11-24',
	            'field_time'=>'01:10:00',
	            'field_enum'=>'testA',
	            'field_object'=>4,
	            'field_calc'=>'123A'
	            
	        ],
	        [
	            'id'=>13,
	            'field_int'=>234,
	            'field_char'=>'DEF',
	            'field_float'=>2.34,
	            'field_text'=>'Lorem ipsum dolor sit amet',
	            'field_datetime'=>'2004-07-01 13:00:00',
	            'field_date'=>'2004-07-01',
	            'field_time'=>'13:00:00',
	            'field_enum'=>'testC',
	            'field_object'=>null,
	            'field_calc'=>'234A'
	        ],
	        [
	            'id'=>14,
	            'field_int'=>555,
	            'field_char'=>'TTT',
	            'field_float'=>5.55,
	            'field_text'=>'dolor sit amet',
	            'field_datetime'=>'2008-05-19 04:15:00',
	            'field_date'=>'2008-05-19',
	            'field_time'=>'04:15:00',
	            'field_enum'=>'testC',
	            'field_object'=>null,
	            'field_calc'=>'555A'
	        ],
	        [
	            'id'=>15,
	            'field_int'=>432,
	            'field_char'=>'XZT',
	            'field_float'=>4.32,
	            'field_text'=>'sed diam voluptua. At vero',
	            'field_datetime'=>'1974-09-15 17:45:00',
	            'field_date'=>'1974-09-15',
	            'field_time'=>'17:45:00',
	            'field_enum'=>'testB',
	            'field_object'=>null,
	            'field_calc'=>'432A'
	        ],
	        [
	            'id'=>16,
	            'field_int'=>700,
	            'field_char'=>null,
	            'field_float'=>7.0,
	            'field_text'=>'consetetur sadipscing elitr',
	            'field_datetime'=>'2004-07-01 17:45:00',
	            'field_date'=>'2004-07-01',
	            'field_time'=>'17:45:00',
	            'field_enum'=>'testC',
	            'field_object'=>null,
	            'field_calc'=>'700A'
	        ],
	        
	        [
	            'id'=>17,
	            'field_int'=>123,
	            'field_char'=>'RRR',
	            'field_float'=>1.23,
	            'field_text'=>'amet. Lorem ipsum dolo',
	            'field_datetime'=>'1978-06-05 11:45:00',
	            'field_date'=>'1978-06-05',
	            'field_time'=>'11:45:00',
	            'field_enum'=>'testC',
	            'field_object'=>3,
	            'field_calc'=>'123A'
	            
	        ],
	        [
	            'id'=>18,
	            'field_int'=>800,
	            'field_char'=>'DEF',
	            'field_float'=>8,
	            'field_text'=>'no sea takimata sanctus',
	            'field_datetime'=>'1974-09-15 17:45:00',
	            'field_date'=>'1974-09-15',
	            'field_time'=>'17:45:00',
	            'field_enum'=>'testB',
	            'field_object'=>4,
	            'field_calc'=>'800A'
	            
	        ],
	        [
	            'id'=>19,
	            'field_int'=>900,
	            'field_char'=>'ZZZ',
	            'field_float'=>9,
	            'field_text'=>'At vero eos et accusam',
	            'field_datetime'=>'1941-06-10 17:45:00',
	            'field_date'=>'1941-06-10',
	            'field_time'=>'17:45:00',
	            'field_enum'=>'testC',
	            'field_object'=>5,
	            'field_calc'=>'900A'
	            
	        ],
	        [
	            'id'=>20,
	            'field_int'=>666,
	            'field_char'=>'ZOO',
	            'field_float'=>6.66,
	            'field_text'=>'sanctus est Lorem ipsum',
	            'field_datetime'=>'1944-08-08 10:45:00',
	            'field_date'=>'1944-08-08',
	            'field_time'=>'10:45:00',
	            'field_enum'=>'testC',
	            'field_object'=>2,
	            'field_calc'=>'666A'
	        ],
	        [
	            'id'=>21,
	            'field_int'=>580,
	            'field_char'=>'DEF',
	            'field_float'=>5.8,
	            'field_text'=>'clita kasd gubergren',
	            'field_datetime'=>'2022-09-15 00:00:00',
	            'field_date'=>'2022-09-15',
	            'field_time'=>'00:00:00',
	            'field_enum'=>'testC',
	            'field_object'=>null,
	            'field_calc'=>'580A'
	        ],
	        [
	            'id'=>22,
	            'field_int'=>432,
	            'field_char'=>'RED',
	            'field_float'=>4.32,
	            'field_text'=>'no sea takimata sanctus est Lorem',
	            'field_datetime'=>'2016-06-17 00:11:00',
	            'field_date'=>'2016-06-17',
	            'field_time'=>'00:11:00',
	            'field_enum'=>'testB',
	            'field_object'=>null,
	            'field_calc'=>'432A'
	        ],
	        [
	            'id'=>23,
	            'field_int'=>345,
	            'field_char'=>'ARG',
	            'field_float'=>3.45,
	            'field_text'=>'dolore magna aliquyam erat',
	            'field_datetime'=>'2000-01-01 00:00:00',
	            'field_date'=>'2000-01-01',
	            'field_time'=>'00:00:00',
	            'field_enum'=>'testC',
	            'field_object'=>null,
	            'field_calc'=>'345A'
	        ],
	        [
	            'id'=>24,
	            'field_int'=>723,
	            'field_char'=>null,
	            'field_float'=>7.23,
	            'field_text'=>'At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren',
	            'field_datetime'=>'1999-12-31 23:59:59',
	            'field_date'=>'1999-12-31',
	            'field_time'=>'23:59:59',
	            'field_enum'=>'testC',
	            'field_object'=>null,
	            'field_calc'=>'723A'
	        ],
	        
	        [
	            'id'=>25,
	            'field_int'=>999,
	            'field_char'=>'DEF',
	            'field_float'=>9.99,
	            'field_text'=>'sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum',
	            'field_datetime'=>'1974-09-15 17:45:00',
	            'field_date'=>'1974-09-15',
	            'field_time'=>'17:45:00',
	            'field_enum'=>'testC',
	            'field_object'=>2,
	            'field_calc'=>'999A'	            
	        ],
	        [
	            'id'=>26,
	            'field_int'=>123,
	            'field_char'=>null,
	            'field_float'=>1.23,
	            'field_text'=>'Lorem ipsum dolor sit amet, consetetur sadipscing',
	            'field_datetime'=>'1999-12-31 23:59:59',
	            'field_date'=>'1999-12-31',
	            'field_time'=>'23:59:59',
	            'field_enum'=>'testB',
	            'field_object'=>null,
	            'field_calc'=>'123A'
	        ],
	    ]);
	}
}