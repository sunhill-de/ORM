<?php
 
/**
 * @file tag_manager.php
 * Provides the tag_manager object for accessing information about tags
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2020-09-13
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerTagTest.php
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\descriptor;
use Sunhill\ORM\Objects\oo_tag;
use Sunhill\ORM\Objects\TagException;

define('TagNamespace','Sunhill\ORM\Objects\oo_tag');

/**
 * The tag_manager is accessed via the Tags facade. It's a singelton class
 */
class tag_manager {
 
    /**
     * Takes the result of a mysql query and forms it into a result descriptor
     * @param $result stdobject The result of the mysql query
     * @return \Manager\Utils\descriptor
     */
   private function get_query_descriptor($result) {
        $part = new descriptor();
        $part->set_id($result->id)->set_name($result->name)->set_parent_id($result->parent_id)
                 ->set_parent_name($result->parent_name)->set_fullpath(static::get_tag_fullpath($result->id));
        return $part;
    }

    private function prepare_query() {
         return DB::table('tags as a')->select(['a.id as id','a.name as name','a.parent_id as parent_id','b.name as parent_name'])
                ->leftJoin('tags as b','b.id','=','a.parent_id');
    }

// ================= Handling of orphaned tags ================================     
     /**
      * Returns the number of orphaned tags (tags that aren't assigned to any objects)
      * @return int
      */
     public function get_orphaned_count() {
         $result = DB::table('tags')->select(DB::raw('count(tags.id) as count'))
         ->leftJoin('tagobjectassigns','tags.id','=','tagobjectassigns.tag_id')
         ->whereNull('tagobjectassigns.tag_id')->first();
         return $result->count;
     }
     
     /**
      * Returns an array of all orphaned tags [id,name,fullname]
      * @return \Manager\Utils\descriptor
      */
     public function get_all_orphaned() {
         $query = static::prepare_query()->leftJoin('tagobjectassigns as c','c.tag_id','=','a.id')
                ->whereNull('c.tag_id')->get();
         $return = [];
         foreach ($query as $result) {
            $return[] = $this->get_query_descriptor($result);
         }
         return $return;
     }
     
     /**
      * Returns the orphaned tag with the given index
      * @param int $index
      * @return \Manager\Utils\descriptor
      */
     public function get_orphaned(int $index) {
         $query = static::prepare_query()->leftJoin('tagobjectassigns as c','c.tag_id','=','a.id')
                ->whereNull('c.tag_id')->offset($index)->limit(1)->first();
         return $this->get_query_descriptor($query);
     }

// ===================== Handling of root tags =========================     
     /**
      * Returns the total count of root tags (tags that don't have a parent tag)
      * @return int
      */
     public function get_root_count() : int {
         $result = DB::table('tags')->select(DB::raw('count(*) as count'))->whereNull('parent_id')->orWhere('parent_id',0)->first();
         return $result->count;
     }
     
     /**
      * Returns the root tag with index $index
      * @param int $index
      */
     public function get_root(int $index) {
        return $this->get_all_root()[$index];
     }
     
     /**
      * Return all root tags
      * @return array of descriptor
      */
     public function get_all_root() {
         $query = $this->prepare_query()->whereNull('a.parent_id')->orWhere('a.parent_id',0)->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = $this->get_query_descriptor($result);
         }
         return $return;         
     }
   
// ===================== Handling of all tags ==============================     
     /**
      * Return the total count of tags
      * @return int
      */
     public function get_count() : int {
         $result = DB::table('tags')->select(DB::raw('count(*) as count'))->first();
         return $result->count;
     }

     /**
      * Returns an array with all tags
      */
     public function get_all_tags($delta=null,$limit=null) {
         $query = $this->prepare_query();
         if (!is_null($delta)) {
             $query = $query->offset($delta);
         }
         if (!is_null($limit)) {
             $query = $query->limit($limit);
         }
         $query = $query->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = $this->get_query_descriptor($result);
         }
         return $return;
     }
     
     /**
      * Return the tag with ID $id 
      * @param int $id
      * @return descriptor
      */
     public function get_tag(int $id) {
         $query = static::prepare_query()->where('a.id',$id)->first();
         if (empty($query)) {
            return null;            
         }
         return $this->get_query_descriptor($query);
     }
     
     /**
      * Returns the fullpath of a tag (that is all parenttags in a row seperated with a dot)
      * @param int $id
      * @return string
      */
     public function get_tag_fullpath(int $id) {
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
     public function change_tag(int $id,array $change) {
         $dbchange = $this->get_db_change($change);
         $this->update_database($id,$dbchange);
         $this->update_tagcache($id,$dbchange);
         $this->update_dependencies($id,$change);
     }

     protected function update_dependencies(int $id,array $change) {
         
     }
     
     /**
      * Decides what kind of information is passed with param $tag and returns the corresponding 
      * tag descriptor array
      * @param unknown $tag
      * @return descriptor
      */
     public function find_tag($tag) {
         if (is_a($tag,TagNamespace)) {
             return $this->get_tag($tag->id);
         } else if (is_int($tag)) {
             // It should be the ID of the Tag
             return $this->get_tag($tag);
         } else if (is_string($tag)) {             // It should be the Name of the Tag             
             return $this->search_tag($tag);
         }
     }
     
     private function get_db_change(array $change) {
         $result = [];
         foreach ($change as $key=>$value) {
             if ($key == 'parent') {
                 $search = static::search_tag($value);
                 $result['parent_id'] = $search->id;
             } else {
                 $result[$key] = $value;
             }
         }
         return $result;
     }
     
     private function update_database(int $id,array $change) {
         DB::table('tags')->where('id',$id)->update($change);
     }
     
     private function update_tagcache(int $id,array $change) {
         static::delete_cache($id);
         static::add_cache_entry($id);
     }
     
     private function add_cache_entry(int $id) {
         $fullpath = static::get_tag_fullpath($id);
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
     
     /**
      * Deletes all tags and clears all refering tables
      */
     public function clear_tags() {
        $this->clear_dependencies();
        $this->clear_cache();
        $this->clear_db();
     }
    
     /**
      * Clears the tag-object-association
      */
     protected function clear_dependencies() {
        DB::table('tagobjectassigns')->delete();
     }
    
     /**
      * Clears the tag cache
      */
     protected function clear_cache() {
        DB::table('tagcache')->delete();
     }
    
     /**
      * Clears the tags table
      */
     protected function clear_db() {
        DB::table('tags')->delete();
     }
    
     /**
      * Deletes the tag with the id $id
      * @param int $id
      */
     public function delete_tag(int $id) {
         $this->delete_dependencies($id);
         static::delete_cache($id);
         static::delete_db($id);
     }
     
     private function delete_dependencies(int $id) {
     }
     
     private function delete_cache(int $id) {
         DB::table('tagcache')->where('tag_id',$id)->delete();         
     }
          
     private function delete_db(int $id) {
         DB::table('tags')->where('id',$id)->delete();
         DB::table('tagobjectassigns')->where('tag_id',$id)->delete();
     }
     
     /**
      * Adds a tag with the given values
      * @param array $values
      */
     public function add_tag($taginfo) {
        if (is_array($taginfo)) {
            $this->add_tag_by_array($taginfo);
        } else if (is_a($taginfo,descriptor::class)) {
            $this->add_tag_by_descriptor($taginfo);
        } else if (is_string($taginfo)) {
            $this->add_tag_by_string($taginfo);
        } else if (is_a($taginfo,oo_tag::class)) {
            $this->add_tag_by_object($taginfo);
        } else {
            throw new TagException(__("Unkown data passed to 'add_tag'."));
        }
     }
    
    /**
     * Returns the id of the given tag or 0 if null is passed
     */
    protected function get_tag_id($parent) {
        if (is_null($parent)) {
            return 0;
        } else {
            return $this->load_tag($parent)->get_id();
        }
    }
    
    /**
     * Adds a tag with the given information to the tags table and add the necessary entries in the tagcache
     * @param $name string The name of the tag
     * @param $parent null|string|oo_tag|int The parent tag (or null if none)
     * @param $options int The options (defaults 0)
     */
    protected function execute_add_tag(string $name,$parent=null,int $options=0) {
        $parent_id = $this->get_tag_id($parent);
        $id = DB::table('tags')->insertGetId(['name'=>$name,'parent_id'=>$parent_id,'options'=>$options]);
    
        $tag = $this->load_tag($id);
        $full_path = $tag->get_fullpath();
	    $fullpath = explode('.',$full_path);
	    while (!empty($fullpath)) {
	        DB::table('tagcache')->insert([
	            'name'=>implode('.',$fullpath),
	            'tag_id'=>$id
	        ]);
	        array_shift($fullpath);
	    }
    }
    
    /**
     * The passed data is an array
     */
     protected function add_tag_by_array(array $values) {
            $this->execute_add_tag($values['name'],isset($values['parent'])?$values['parent']:null);
     }   
    
     /**
      * The passed data is a descriptor
      */
     protected function add_tag_by_descriptor(descriptor $descriptor) {
        $this->execute_add_tag($descriptor->name,$descriptor->parent);
     }
    
     /**
      * The passed data is a string
      */
     protected function add_tag_by_string(string $tag) {
         $tag_parts = explode('.',$tag);
         $tag_name = array_shift($tag_parts);
         $this->execute_add_tag($tag_name,implode('.',$tag_parts));
     }
    
     /**
      * The passed data is a oo_tag object
      */
     protected function add_tag_by_object(oo_tag $tag) {
        $this->execute_add_tag($tag->get_name(),$tag->get_parent());
     }
    
     /**
      * Lists tags with a condition and an (optional) delta and limit
      */
     public function list_tags(string $condition,int $delta=0,int $limit=-1) {

         $query = $this->prepare_query()->whereRaw('a.'.$condition);
         if ($delta) {
            $query = $query->offset($delta);
         }
         if ($limit > -1) {
             $query = $query->limit($limit);
         }
         $results = $query->get();
         $return = [];
         foreach ($results as $result) {
             $return[] = $this->get_query_descriptor($result);
         }
         return $return;
     }
     
     protected function do_search_tag(string $name) {
         return $this->prepare_query()
                ->join('tagcache', 'tagcache.tag_id','=','a.id')
                ->where('tagcache.name',$name)->get();         
     }
     
     /**
      * Searches for a tag with the name $name and returns all found tag descriptors
      */
     public function search_tag(string $name) {
         $query = $this->do_search_tag($name);
         $return = [];
         foreach ($query as $result) {
             $return[] = $this->get_query_descriptor($result);
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
     * @param $tag string|int|oo_tag
     * If $tag is a string then it searches for the fitting tag. 
     * If $tag is a int then it searches for the tag with
     * If $tag is a oo_tag object it returns $tag
     * @return oo_tag
     * @throws TagException if tag is not found or is not unique
     */
     public function load_tag($tag) {
         if (is_a($tag,oo_tag::class)) {
             return $tag;
         }
         if (is_string($tag)) {
             $result = $this->do_search_tag($tag);
             if (count($result) == 0) {
                 throw new TagException("Tag '$tag' not found.");
             } else if (count($result) > 1) {
                 throw new TagException("Tag '$tag' not unique.");
             }
             $tag_id = $result[0]->id;
         } else if (is_int($tag)) {
             $tag_id = $tag;
         }
         $tag = new oo_tag($tag_id);
         return $tag;
     }
 }
 
