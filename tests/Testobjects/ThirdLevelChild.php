<?php
/**
 * @file ThirdLevelChild.php
 * Provides the test object ThirdLevelChild is derrived from SecondLevelChild
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\PropertyList;

class ThirdLevelChild extends SecondLevelChild 
{

    protected static function setupProperties(PropertyList $list)
    {
		$list->integer('childchildint');
		$list->varchar('childchildchar');
		$list->object('thirdlevelobject');
		$list->arrayofstrings('thirdlevelsarray');
	}
	
	public function postPromotion($from) 
	{
	    if (is_a($from,SecondLevelChild::class)) {
	        $this->childchildint = $this->childint * 2;
	    } elseif (is_a($from,ReferenceOnly::class)) {	        
	        $this->childint = 2;
	        $this->childchildint = $this->childint * 2;	        
	    } else {
	        throw new \Exception("OOPS");
	    }
	}
	
	protected static function setupInfos()
	{
	    static::addInfo('name', 'thirdlevelchild');
	    static::addInfo('table', 'thirdlevelchildren');
	    static::addInfo('name_s', 'thirdlevelchild');
	    static::addInfo('name_p', 'thirdlevelchild');
	    static::addInfo('description', 'Another test class. A derrived class with all some properties');
	    static::addInfo('options', 0);
	}
	
}

