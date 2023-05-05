<?php
/**
 * @file Tag.php
 * Provides the object Tag
 * Lang en
 * Reviewstatus: 2021-10-06
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: 
 */
namespace Sunhill\ORM\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Loggable;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Tags;

define('TO_LEAFABLE',0x0001);

class Tag extends Loggable 
{
	
    const TO_LEAFABLE = 0x0001;
    
	protected $tag_id;
	
	protected $options = 0;
	
	protected $parent = null;
	
	protected $name = '';
	
	protected $state = 'normal';
	
	/**
	 * Returns the tag-ID
	 * @return number
	 */
	public function getID(): int
    {
		if ($this->tag_id) {
			return $this->tag_id;
		} else {
			return 0;
		}
	}
	
	public function load(int $id)
	{
	   $this->state = 'preloading';
	   $this->tag_id = $id;
	}
	
	protected function checkLoadingState()
	{
	    if ($this->state == 'preloading') {
	        $query = DB::table('tags')->where('id',$this->getID())->first();
	        $this->name = $query->name;
	        $this->options = $query->options;
	        if ($query->parent_id) {
	           $this->parent = new Tag();
	           $this->parent->load($query->parent_id);
	           $this->state = 'normal';
	        }
	    }
	}
	
	/**
	 * Returns the parent tag or null if there is none
	 * @return Tag|null
	 */
	public function getParent(): ?Tag
    {
		$this->checkLoadingState();
        return $this->parent;
	}
	
	/**
	 * Setzt das Eltern-Tag
	 * @param Tag $parent
	 * @return \Crawler\Tag
	 */
	public function setParent(Tag $parent): Tag
    {
		$this->parent = $parent;
		return $this;		
	}
	
	/**
	 * returns the simple name of the tag
	 * @return string
	 */
	public function getName(): string 
    {
        $this->checkLoadingState();
        return $this->name;
	}
	
	/**
	 * Sets the simple name of the tag
	 * @param string $name
	 * @return Tag
	 */
	public function setName($name): Tag
    {
		$this->name = $name;
		return $this;
	}
	
	public function getOptions(): int
    {
        $this->checkLoadingState();
        return $this->options;
	}
	
	public function setOptions(int $options): Tag 
    {
		$this->options = $options;
		return $this;
	}
	
	public function isLeafable(): bool
    {
        $this->checkLoadingState();
        return $this->options & TO_LEAFABLE;
	}
	
	public function set_Leafable(): Tag
    {
		$this->options &= TO_LEAFABLE;
        return $this;
	}
	
	public function unsetLeafable(): Tag
    {
		$this->options &= !TO_LEAFABLE;
        return $this;
	}
		
	/**
	 * Creates a fully assembled path of this tag and all parent tags
	 * @return string
	 */
	public function getFullPath() 
    {
        $this->checkLoadingState();
        if (is_null($this->parent)) {
			return $this->getName();
		} else {
			return $this->parent->getFullPath().".".$this->getName();
		}
	}
		
}
