<?php
/**
 * @file ScenarioWithTags.php
 * An extension to scenarios that handle tags
 * Lang en
 * Reviewstatus: 2021-08-05
 * Localization: none required
 * Documentation: complete
 * Tests: tests/Unit/Scenarios/ScenariosWithTagsTest.php, tests/Feature/Scenarios/ScenarioWithTagsTest.php
 * Coverage: unknown
 * Dependencies: Functioning tag subsystem
 */

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\ORM\Facades\Tags;
use Sunhill\Basic\SunhillException;

trait ScenarioWithTags {

  /**
   * This is called by the Test to setup set Tags
   */
  protected function SetUpTags() {
    Tags::clearTags();  // Delete all tags and clear the tag cache
    $tags = $this->GetTags();
    foreach ($tags as $tag) {
      $this->SetupTag($tag);
    }
  }
  
  protected function SetupTag(string $tag) {
     Tags::addTag($tag);
  }
  
  /**
   * This method returns an array of tags. The tags can be seperated by dots, then a tag hirarchy is build
   * Example:
   *  [
   *    'TagA',
   *    'TagB.TagC'
   *  ]
   */
  abstract function GetTags();
}
