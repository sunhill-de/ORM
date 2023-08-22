<?php
 
/**
 * @file TagManager.php
 * Provides the TagManager object for accessing information about tags
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-10-10
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerTagTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Objects\TagException;
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Facades\Storage;

define('TagNamespace','Sunhill\ORM\Objects\Tag');

/**
 * The TagManager is accessed via the Tags facade. It's a singelton class
 */
class TagManager 
{
 
     /**
      * Searches for all tags that fit to given $input string as name.
      * If you search for "TagA" it will find "TagA" and "TagB.TagA" (but not ("TagA.TagB")
      *  
      * @param unknown $input
      * @return \Illuminate\Support\Collection
      */
     public function searchTag(string $input)
     {
        return $this->query()->where('any_path',$input)->get();    
     }
     
     /**
      * Searches for the Tag that fits best to the given input.
      * If input is a int it assumes this as the id of the tag (and loads it)
      * If input is already a Tag there is nothing to do
      * If input is a string then it searches for the Tag that fits to this string (@see searchTag())
      * 
      * @param unknown $input
      * @throws TagException When the search fails or is not unique 
      * @return NULL|\Sunhill\ORM\Objects\Tag|unknown
      */
     public function getTag($input): Tag
     {
         if (is_int($input)) {
             $tag = $this->loadTag($input);
             if (empty($tag)) {
                 throw new TagException("Tag with id '$input' was not found.");                 
             }
             return $this->loadTag($input);
         } else if (is_a($input,Tag::class)) {
             return $input;
         } else if (is_string($input)) {
             $result = $this->searchTag($input);
             if (count($result) == 0) {
                 throw new TagException("Tag '$input' not found.");
             } else if (count($result) > 1) {
                 throw new TagException("Tag '$input' not unique.");                 
             }
             return $this->loadTag($result[0]->id);
         }
     }
     
     /**
      * Loads the tag with the given id when it exists.
      * 
      * @param int $id
      * @return \Sunhill\ORM\Objects\Tag|NULL return null when this tag doesnt exist.
      */
     public function loadTag(int $id)
     {
         if (count($this->query()->where('id',$id)->get())) {
             $tag = new Tag();
             $tag->load($id);
             return $tag;
         }
         return null;
     }
     
     public function deleteTag(int $id)
     {
        $this->query()->where('id',$id)->delete();    
     }
     
     /**
      * Returns the TagQuery object for searching
      * 
      * @return BasicQuery
      */
     public function query(): BasicQuery
     {
         return Storage::tagQuery();
     }
 }
 
