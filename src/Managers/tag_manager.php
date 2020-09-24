<?php
 
/**
 * @file class_manager.php
 * Provides the class_manager object for accessing information about the orm classes
 * Lang en
 * Reviewstatus: 2020-09-13
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
 
class tag_manager {
 
    /**
     * Takes the result of a mysql query and forms it into a result descriptor
     * @param $result stdobject The result of the mysql query
     * @return \Manager\Utils\descriptor
     */
    static private function get_query_descriptor($result) {
        $part = new \Manager\Utils\descriptor();
        $part->set_id($result->id)->set_name($result->name)->set_parent_id($result->parent_id)
                 ->set_parent_name($result->parent_name)->set_fullpath(static::get_tag_fullpath($result->id));
        return $part;
    }

    static private function prepare_query() {
         return DB::table('tags as a')->select(['a.id as id','a.name as name','a.parent_id as parent_id','b.name as parent_name'])
                ->leftJoin('tags as b','b.id','=','a.parent_id');
    }

// ================= Handling of orphaned tags ================================     
     /**
      * Returns the number of orphaned tags (tags that aren't assigned to any objects)
      * @return int
      */
     static public function get_orphaned_count() {
         $result = DB::table('tags')->select(DB::raw('count(tags.id) as count'))
         ->leftJoin('tagobjectassigns','tags.id','=','tagobjectassigns.tag_id')
         ->whereNull('tagobjectassigns.tag_id')->first();
         return $result->count;
     }
     
     /**
      * Returns an array of all orphaned tags [id,name,fullname]
      * @return \Manager\Utils\descriptor
      */
     static public function get_all_orphaned() {
         $query = static::prepare_query()->leftJoin('tagobjectassigns as c','c.tag_id','=','a.id')
                ->whereNull('c.tag_id')->get();
         $return = [];
         foreach ($query as $result) {
            $return[] = static::get_query_descriptor($result);
         }
         return $return;
     }
     
     /**
      * Returns the orphaned tag with the given index
      * @param int $index
      * @return \Manager\Utils\descriptor
      */
     static public function get_orphaned(int $index) {
         $query = static::prepare_query()->leftJoin('tagobjectassigns as c','c.tag_id','=','a.id')
                ->whereNull('c.tag_id')->offset($index)->limit(1)->first();
         return static::get_query_descriptor($query);
     }

// ===================== Handling of root tags =========================     
     /**
      * Returns the total count of root tags (tags that don't have a parent tag)
      * @return int
      */
     static public function get_root_count() : int {
         $result = DB::table('tags')->select(DB::raw('count(*) as count'))->whereNull('parent_id')->orWhere('parent_id',0)->first();
         return $result->count;
     }
     
     /**
      * Returns the root tag with index $index
      * @param int $index
      */
     static public function get_root(int $index) {
        return static::get_all_root()[$index];
     }
     
     /**
      * Return all root tags
      * @return array of descriptor

      */
     static public function get_all_root() {
         $query = static::prepare_query()->whereNull('a.parent_id')->orWhere('a.parent_id',0)->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = static::get_query_descriptor($result);
         }
         return $return;         
     }
   
// ===================== Handling of all tags ==============================     
     /**
      * Return the total count of tags
      * @return int
      */
     static public function get_count() : int {
         $result = DB::table('tags')->select(DB::raw('count(*) as count'))->first();
         return $result->count;
     }

     /**
      * Returns an array with all tags
      */
     static public function get_all_tags($delta=null,$limit=null) {
         $query = static::prepare_query();
         if (!is_null($delta)) {
             $query = $query->offset($delta);
         }
         if (!is_null($limit)) {
             $query = $query->limit($limit);
         }
         $query = $query->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = static::get_query_descriptor($result);
         }
         return $return;
     }
     
     /**
      * Return the tag with ID $id 
      * @param int $id
      * @return descriptor
      */
     static public function get_tag(int $id) {
         $query = static::prepare_query()->where('a.id',$id)->first();
         if (empty($query)) {
            return null;            
         }
         return static::get_query_descriptor($query);
     }
     
     /**
      * Returns the fullpath of a tag (that is all parenttags in a row seperated with a dot)
      * @param int $id
      * @return string
      */
     static public function get_tag_fullpath(int $id) {
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
     static public function change_tag(int $id,array $change) {
         $dbchange = static::get_db_change($change);
         static::update_links($id,$change);
         static::update_dirs($id,$change);
         static::update_database($id,$dbchange);
         static::update_tagcache($id,$dbchange);
     }
     
     static public function get_links_to_tag($tag) {
         $tag_descriptor = static::find_tag($tag);
         $results = DB::table('linkreferences')->where('key','tag')->where('value',$tag_descriptor['fullpath'])->get();
         $return = [];
         foreach ($results as $result) {
             $return[] = \Sunhill\ORM\Objects\oo_object::load_object_of($result->link_id);
         }
         return $return;
     }
     
     /**
      * Decides what kind of information is passed with param $tag and returns the corresponding 
      * tag descriptor array
      * @param unknown $tag
      * @return descriptor
      */
     static public function find_tag($tag) {
         if (is_a($tag,'\Sunhill\ORM\Objects\oo_tag')) {
             return static::get_tag($tag->id);
         } else if (is_int($tag)) {
             // It should be the ID of the Tag
             return static::get_tag($tag);
         } else if (is_string($tag)) {             // It should be the Name of the Tag             
             return static::search_tag($tag);
         }
     }
     
     static private function get_db_change(array $change) {
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
     
     static private function update_database(int $id,array $change) {
         DB::table('tags')->where('id',$id)->update($change);
     }
     
     static private function update_tagcache(int $id,array $change) {
         static::delete_cache($id);
         static::add_cache_entry($id);
     }
     
     static private function add_cache_entry(int $id) {
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
     
     static private function update_dirs(int $id,array $change) {
        $dirs = \Manager\Objects\dir::search()->where('tags','has',static::get_tag($id)->name)->get();
        foreach ($dirs as $dir) {
            $dir = \Sunhill\ORM\Objects\oo_object::load_object_of($dir);
            static::update_dir($id,$dir,$change);
        }
     }

     static private function update_dir(int $id,\Manager\Objects\dir $dir,array $change) {
        if (isset($change['name'])) {
            $dir->rename_to($change['name']);
        } else if (isset($change['parent'])) {
            static::move_to($dir,static::get_tag(static::get_tag($id)->parent_id),static::search_tag($change['parent']));
        }
     }

     static private function move_to($dir,$from,$to) {
        $current = $dir->parent_dir->full_path;
        $from_part = str_replace('.','/',$from->fullpath);
        $to_part   = str_replace('.','/',$to->fullpath);
        $current   = str_replace($from_part,$to_part,$current);
        $dir->move_to($current);
     }

     static private function update_links(int $id,array $change) {
         $tag = static::get_tag($id);
         if (isset($change['name'])) {
             $newname = $change['name'];
         } else {
             $newname = $tag->name;
         }
         if (isset($change['parent_id'])) {
             $newparent = static::get_tag_fullpath($change['parent_id']);
         } else {
             $newparent = static::get_tag_fullpath($tag->parent_id);             
         }
         if (!empty($newparent)) {
             $newtag = $newparent.'.'.$newname;
         } else {
             $newtag = $newname;
         }
         $files = static::search_files($tag->fullpath);
         if (empty($files)) {
             return;
         }
         foreach ($files as $file) {
            static::update_file($file,$tag->fullpath,$newtag);        
         }
     }
     
     static private function update_file(\Manager\Objects\file $file,string $tag,string $newtag) {
        $file->tag_changed([['from'=>$tag,'to'=>$newtag]]);
     }
     
     static private function update_link(\Manager\Objects\link $link,string $tag,string $newtag) {
        $link->tag_changed([['from'=>$tag,'to'=>$newtag]]);    
     }
     
     /**
      * Deletes the tag with the id $id
      * @param int $id
      */
     static public function delete_tag(int $id) {
         static::delete_links($id);
         static::delete_cache($id);
         static::delete_db($id);
     }
     
     static private function delete_links(int $id) {
        $dirs = \Manager\Objects\dir::search()->where('tags','has',static::get_tag($id)->name)->get();
        foreach ($dirs as $dir) {
            $dir_obj = \Sunhill\ORM\Objects\oo_object::load_object_of($dir);
            static::delete_dir($dir_obj);
        }
     }
     
     static private function delete_dir(\Manager\Objects\dir $dir) {
        $dir->erase();
     }

     static private function delete_cache(int $id) {
         DB::table('tagcache')->where('tag_id',$id)->delete();         
     }
          
     static private function delete_db(int $id) {
         DB::table('tags')->where('id',$id)->delete();
         DB::table('tagobjectassigns')->where('tag_id',$id)->delete();
     }
     
     /**(
      * Adds a tag with the given values
      * @param array $values
      */
     static public function add_tag(array $values) {
            $tag = new \Sunhill\ORM\Objects\oo_tag();
            $tag->name = $values['name'];
            if (isset($values['parent'])) {
               $parent = static::search_tag($values['parent'])->id;
               $parent_tag = \Sunhill\ORM\Objects\oo_tag::load_tag($parent);
                $tag->parent = $parent_tag;
            }
            $tag->commit();  
     }
     
     /**
      * Lists tags with a condition and an (optional) delta and limit
      */
     static public function list_tags(string $condition,int $delta=0,int $limit=-1) {

         $query = static::prepare_query()->whereRaw('a.'.$condition);
         if ($delta) {
            $query = $query->offset($delta);
         }
         if ($limit > -1) {
             $query = $query->limit($limit);
         }
         $results = $query->get();
         $return = [];
         foreach ($results as $result) {
             $return[] = static::get_query_descriptor($result);
         }
         return $return;
     }
     
     /**
      * Searches for a tag with the name $name and returns all found tag descriptors
      */
     static public function search_tag(string $name) {
        $query = static::prepare_query()                    
                ->join('tagcache', 'tagcache.tag_id','=','a.id')
                ->where('tagcache.name',$name)->get();
         $return = [];
         foreach ($query as $result) {
             $return[] = static::get_query_descriptor($result);
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
      * Returns all files that have the given tag 
      * @param string $name
      * @throws \Exception
      * @return NULL|\Sunhill\ORM\Objects\oo_object[]
      */
     static public function search_files(string $name) {
        $result = static::search_tag($name);
        if (empty($result)) {
            return null;
        }
        if (!$result->id) {
            throw new \Exception("The tag name '$name' is not unique");
        }
        $result = \Manager\Objects\file::search()->where('tags','has',$name)->get();
        if (is_null($result)) {
            return null;
        } else if (is_array($result)) {
            $return = [];
            foreach ($result as $file) {
                $return[] = \Sunhill\ORM\Objects\oo_object::load_object_of($file);
            }
            return $return;
        } else {
            return [\Sunhill\ORM\Objects\oo_object::load_object_of($result)];
        }
     }

     /**
      * Returns the dir_descriptor for this tag
      * @param $tag int|string|oo_tag the tag for which the descriptor should be returned
      * @return dir_descriptor
      */
     static public function get_dir_descriptor($tag) {
         $tag = static::find_tag($tag);
         $tag_array = [$tag->name=>$tag->id];
         while ($tag->parent_id) {
             $tag = static::get_tag($tag->parent_id);
             $tag_array[$tag->name] = $tag->id;
         }
         $tag_array = array_reverse($tag_array);
         $return = new \Manager\Utils\dir_descriptor(implode('/',array_keys($tag_array)));
         $i = 0;
         foreach ($tag_array as $key => $id) {
            $return[$i++]->tag_id = $id;
         }
         return $return;
     }
 }
 