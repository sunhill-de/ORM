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

define('TagNamespace','Sunhill\ORM\Objects\Tag');

/**
 * The TagManager is accessed via the Tags facade. It's a singelton class
 */
class TagManager 
{
 
    /**
     * Takes the result of a mysql query and forms it into a result Descriptor
     * @param $result stdobject The result of the mysql query
     * @return \Manager\Utils\Descriptor
     */
   private function getQueryDescriptor($result): Descriptor 
   {
        $part = new Descriptor();
        $part->set_id($result->id)->set_name($result->name)->set_parent_id($result->parent_id)
                 ->set_parent_name($result->parent_name)->set_fullpath(static::getTagFullpath($result->id));
        return $part;
    }

    private function prepareQuery() {
         return DB::table('tags as a')->select(['a.id as id','a.name as name','a.parent_id as parent_id','b.name as parent_name'])
                ->leftJoin('tags as b','b.id','=','a.parent_id');
    }

// ================= Handling of orphaned tags ================================     
     /**
      * Returns the number of orphaned tags (tags that aren't assigned to any objects)
      * @return int
      */
     public function getOrphanedCount(): int 
     {
         $result = DB::table('tags')->select(DB::raw('count(tags.id) as count'))
         ->leftJoin('tagobjectassigns','tags.id','=','tagobjectassigns.tag_id')
         ->whereNull('tagobjectassigns.tag_id')->first();
         return $result->count;
     }
     
     /**
      * Returns an array of all orphaned tags [id,name,fullname]
      * @return \Manager\Utils\Descriptor
      */
     public function getAllOrphaned(): array 
     {
         $query = static::prepareQuery()->leftJoin('tagobjectassigns as c','c.tag_id','=','a.id')
                ->whereNull('c.tag_id')->get();
         $return = [];
         foreach ($query as $result) {
            $return[] = $this->getQueryDescriptor($result);
         }
         return $return;
     }
     
     /**
      * Returns the orphaned tag with the given index
      * @param int $index
      * @return \Manager\Utils\Descriptor
      */
     public function getOrphaned(int $index): Descriptor
     {
         $query = static::prepareQuery()->leftJoin('tagobjectassigns as c','c.tag_id','=','a.id')
                ->whereNull('c.tag_id')->offset($index)->limit(1)->first();
         return $this->getQueryDescriptor($query);
     }

// ===================== Handling of root tags =========================     
     /**
      * Returns the total count of root tags (tags that don't have a parent tag)
      * @return int
      */
     public function getRootCount() : int 
     {
         $result = DB::table('tags')->select(DB::raw('count(*) as count'))->whereNull('parent_id')->orWhere('parent_id',0)->first();
         return $result->count;
     }
     
     /**
      * Returns the root tag with index $index
      * @param int $index
      */
     public function getRoot(int $index) 
     {
        return $this->getAllRoot()[$index];
     }
     
     /**
      * Return all root tags
      * @return array of Descriptor
      */
     public function getAllRoot(): array 
     {
         $query = $this->prepareQuery()->whereNull('a.parent_id')->orWhere('a.parent_id',0)->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = $this->getQueryDescriptor($result);
         }
         return $return;         
     }
   
// ===================== Handling of all tags ==============================     
     /**
      * Return the total count of tags
      * @return int
      */
     public function getCount(): int 
     {
         $result = DB::table('tags')->select(DB::raw('count(*) as count'))->first();
         return $result->count;
     }

     /**
      * Returns an array with all tags
      */
     public function getAllTags($delta = null, $limit = null) 
     {
         $query = $this->prepareQuery();
         if (!is_null($delta)) {
             $query = $query->offset($delta);
         }
         if (!is_null($limit)) {
             $query = $query->limit($limit);
         }
         $query = $query->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = $this->getQueryDescriptor($result);
         }
         return $return;
     }
     
     /**
      * Return the tag with ID $id 
      * @param int $id
      * @return Descriptor
      */
     public function getTag(int $id) 
     {
         $query = static::prepareQuery()->where('a.id',$id)->first();
         if (empty($query)) {
            return null;            
         }
         return $this->getQueryDescriptor($query);
     }
     
     /**
      * Returns the fullpath of a tag (that is all parenttags in a row seperated with a dot)
      * @param int $id
      * @return string
      */
     public function getTagFullpath(int $id): string 
     {
         $result = '';
         while ($id) {
             $query = DB::table('tags')->where('id',$id)->first();
             $id = $query->parent_id;
             if (empty($result)) {                 
                 $result = $query->name;
             } else {
                 $result = $query->name.'.'.$result;
             }
         }
         return $result;
     }
     
     /**
      * Changes the tag with the id $id
      * @param int $id
      * @param array $change
      */
     public function changeTag(int $id, array $change) 
     {
         $dbchange = $this->getDBChange($change);
         $this->updateDatabase($id,$dbchange);
         $this->updateTagcache($id,$dbchange);
         $this->updateDependencies($id,$change);
     }

     protected function updateDependencies(int $id, array $change) 
     {
         
     }
     
     /**
      * Decides what kind of information is passed with param $tag and returns the corresponding 
      * tag Descriptor array
      * @param unknown $tag
      * @return Descriptor
      */
     public function findTag($tag) 
     {
         if (is_a($tag,TagNamespace)) {
             return $this->getTag($tag->id);
         } else if (is_int($tag)) {
             // It should be the ID of the Tag
             return $this->getTag($tag);
         } else if (is_string($tag)) {             // It should be the Name of the Tag             
             return $this->searchTag($tag);
         }
     }
     
     private function getDBChange(array $change) 
     {
         $result = [];
         foreach ($change as $key=>$value) {
             if ($key == 'parent') {
                 $search = static::searchTag($value);
                 $result['parent_id'] = $search->id;
             } else {
                 $result[$key] = $value;
             }
         }
         return $result;
     }
     
     private function updateDatabase(int $id, array $change) 
     {
         DB::table('tags')->where('id',$id)->update($change);
     }
     
     private function updateTagcache(int $id, array $change) 
     {
         static::deleteCache($id);
         static::addCacheEntry($id);
     }
     
     private function addCacheEntry(int $id) 
     {
         $fullpath = static::getTagFullpath($id);
         $parts = explode('.',$fullpath);
         $tag_name = '';
         do {
             $rest = array_pop($parts);
             if (!empty($tag_name)) {
                 $tag_name = $rest.'.'.$tag_name;
             } else {
                 $tag_name = $rest;
             }
             db::table('tagcache')->insert(['tag_id'=>$id,'name'=>$tag_name]);
         } while (!empty($parts));         
     }
     
    public function clearTags() 
    {
         $this->clearDependencies();
         $this->clearCache();
         $this->clearDB();         
     }
     
     /**
      * Clears the tag-object-association
      */
     protected function clearDependencies() 
     {
        DB::table('tagobjectassigns')->delete();
     }
    
     /**
      * Clears the tag cache
      */
     protected function clearCache() 
     {
        DB::table('tagcache')->delete();
     }
    
     /**
      * Clears the tags table
      */
     protected function clearDB() 
     {
        DB::table('tags')->delete();
     }
    
     /**
      * Deletes the tag with the id $id
      * @param int $id
      */
     public function deleteTag(int $id) 
     {
         $this->deleteDependencies($id);
         static::deleteCache($id);
         static::deleteDB($id);
     }
     
     private function deleteDependencies(int $id) 
     {
     }
     
     private function deleteCache(int $id) 
     {
         DB::table('tagcache')->where('tag_id',$id)->delete();         
     }
          
     private function deleteDB(int $id) 
     {
         DB::table('tags')->where('id',$id)->delete();
         DB::table('tagobjectassigns')->where('tag_id',$id)->delete();
     }
     
     /**
      * Adds a tag with the given values
      * @param array $values
      */
     public function addTag($taginfo) 
     {
        if (is_array($taginfo)) {
            $this->addTagByArray($taginfo);
        } else if (is_a($taginfo,Descriptor::class)) {
            $this->addTagByDescriptor($taginfo);
        } else if (is_string($taginfo)) {
            $this->addTagByString($taginfo);
        } else if (is_a($taginfo,Tag::class)) {
            $this->addTagByObject($taginfo);
        } else {
            throw new TagException(__("Unkown data passed to 'add_tag'."));
        }
     }
    
    /**
     * Returns the id of the given tag or 0 if null is passed
     */
    protected function getTagID($parent) 
    {
        if (is_null($parent) || empty($parent)) {
            return 0;
        } else {
            return $this->loadTag($parent)->getID();
        }
    }
    
    /**
     * Adds a tag with the given information to the tags table and add the necessary entries in the tagcache
     * @param $name string The name of the tag
     * @param $parent null|string|Tag|int The parent tag (or null if none)
     * @param $options int The options (defaults 0)
     */
    protected function executeAddTag(string $name, $parent = null, int $options = 0) 
    {
        $parent_id = $this->GetParent($parent);
        $id = DB::table('tags')->insertGetId(['name'=>$name,'parent_id'=>$parent_id,'options'=>$options]);
        $tag = $this->loadTag($id);
        $full_path = $tag->getFullPath();
	    $fullpath = explode('.',$full_path);
	    while (!empty($fullpath)) {
	        DB::table('tagcache')->insert([
	            'name'=>implode('.',$fullpath),
	            'tag_id'=>$id
	        ]);
	        array_shift($fullpath);
	    }
	    return $id;
    }
    
    protected function GetParent($parent) 
    {
        if (!empty($parent)) {
            if (is_a($parent,Descriptor::class)) {
                $name = $parent->name;
                if (empty($name)) {
                    return 0;           
                }
                $parent= $parent->name;
            }
            $parent_id = $this->searchTag($parent);
            if (empty($parent_id)) {
                return $this->addTagByString($parent);
            } else {
                return $parent_id->id;
            }
        } else {
            return 0;
        }        
    }
    
    /**
     * The passed data is an array
     */
     protected function addTagByArray(array $values) 
     {
            $this->executeAddTag($values['name'],isset($values['parent'])?$values['parent']:null);
     }   
    
     /**
      * The passed data is a Descriptor
      */
     protected function addTagByDescriptor(Descriptor $Descriptor) 
     {
        $this->executeAddTag($Descriptor->name,$Descriptor->assertHasKey('parent')?$Descriptor->parent:null);
     }
    
     /**
      * The passed data is a string
      */
     protected function addTagByString(string $tag) 
     {
         $tag_parts = explode('.',$tag);
         $tag_name = array_pop($tag_parts);
         return $this->executeAddTag($tag_name,implode('.',$tag_parts));
     }
    
     /**
      * The passed data is a Tag object
      */
     protected function addTagByObject(Tag $tag) 
     {
        $parent = $tag->getParent();
        $this->executeAddTag($tag->getName(),$parent?$parent->getFullPath():null);
     }
    
     /**
      * Lists tags with a condition and an (optional) delta and limit
      */
     public function listTags(string $condition, int $delta = 0, int $limit = -1) 
     {

         $query = $this->prepareQuery()->whereRaw('a.'.$condition);
         if ($delta) {
            $query = $query->offset($delta);
         }
         if ($limit > -1) {
             $query = $query->limit($limit);
         }
         $results = $query->get();
         $return = [];
         foreach ($results as $result) {
             $return[] = $this->getQueryDescriptor($result);
         }
         return $return;
     }
     
     protected function doSearchTag(string $name) 
     {
         return $this->prepareQuery()
                ->join('tagcache', 'tagcache.tag_id','=','a.id')
                ->where('tagcache.name',$name)->get();         
     }
     
     /**
      * Searches for a tag with the name $name and returns all found tag descriptors
      */
     public function searchTag(string $name) 
     {
         $query = $this->doSearchTag($name);
         $return = [];
         foreach ($query as $result) {
             $return[] = $this->getQueryDescriptor($result);
         }
         switch (count($return)) {
             case 0:
                 return;
             case 1:
                 return $return[0];
             default:
                 return $return;
         }
     }
     
    /**
     * Loads the given tag $tag.
     * @param $tag string|int|Tag
     * If $tag is a string then it searches for the fitting tag. 
     * If $tag is a int then it searches for the tag with
     * If $tag is a Tag object it returns $tag
     * @return Tag
     * @throws TagException if tag is not found or is not unique
     */
     public function loadTag($tag) 
     {
         if (is_a($tag,Tag::class)) {
             return $tag;
         }
         if (is_string($tag)) {
             $result = $this->doSearchTag($tag);
             if (count($result) == 0) {
                 throw new TagException(__("Tag ':tag' not found.",['tag'=>$tag]));
             } else if (count($result) > 1) {
                 throw new TagException(__("Tag ':tag' not unique.",['tag'=>$tag]));
             }
             $tag_id = $result[0]->id;
         } else if (is_int($tag)) {
             $tag_id = $tag;
         }
         $tag = new Tag();
         $tag->load($tag_id);
         return $tag;
     }
 }
 
