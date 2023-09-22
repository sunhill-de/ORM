<?php
 
/**
 * @file AttributeManager.php
 * Provides the AttributeManager object for accessing information about attributes
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-08
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerTagTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Managers\Exceptions\InvalidAttributeIDException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Managers\Exceptions\InvalidTypeException;
use Sunhill\ORM\Managers\Exceptions\NotAnORMClassException;
use Sunhill\ORM\Facades\Classes;

/**
 * The AttributeManager is accessed via the Attributes facade. It's a singelton class
 */
class AttributeManager 
{

    public function getAvaiableAttributesForClass(string $class, array $without = [])
    {
        $attributes = $this->query()->where('allowed_classes','matches',$class);
        if (!empty($without)) {
            foreach ($without as $entry) {
                $attributes->whereNot('name', $entry);                
            }
        }
        return $attributes->get();
    }
        
    public function query(): BasicQuery
    {
        return Storage::attributeQuery();
    }
    
    public function deleteAttribute(int $id)
    {
        if (empty($attribute = $this->query()->where('id',$id)->first())) {
            throw new InvalidAttributeIDException("The given ID '$id' is invalid");
        }
        Schema::drop('attr_'.$attribute->name);
        DB::table('attributes')->where('id',$id)->delete();
        DB::table('attributeobjectassigns')->where('attribute_id',$id)->delete();
    }
    
    protected function checkClasses(array $classes)
    {
        foreach ($classes as $class) {
            if (!Classes::searchClass($class)) {
                throw new NotAnORMClassException("'$class' is not an ORM-Class");
            }
        }
    }
    
    protected function checkName(string $name)
    {
        if (!preg_match("#^[a-zA-Z0-9_]+$#",$name) || ($name[0] == '_')) {
            throw new InvalidNameException("The name '$name' is not allowed for properties");
        }
    }
    
    protected function checkType(string $type)
    {
        if (!in_array($type,['integer','string','float','text'])) {
            throw new InvalidTypeException("The type '$type' is invalid.");
        }
    }
    
    public function addAttribute(string $name, string $type, array $allowed_classes)
    {
        $this->checkClasses($allowed_classes);
        $this->checkName($name);
        $this->checkType($type);
        $allowed = Str::finish(Str::start(implode('|',$allowed_classes),'|'),'|');
        DB::table('attributes')->insert(['name'=>$name,'type'=>$type,'allowed_classes'=>$allowed]);        
        Schema::create('attr_'.$name, function($table) use ($type) {
            $table->integer('object_id')->primary();
            switch ($type) {
                case 'integer':
                    $table->integer('value');
                    break;
                case 'string':
                    $table->string('value');
                    break;
                case 'float':
                    $table->float('value');
                    break;
                case 'text':
                    $table->text('value');
                    break;
            }
        });
        return DB::getPdo()->lastInsertId();
    }
    
    protected function changeAttributeName($id, $from, $to)
    {
        $this->checkName($to);
        Schema::rename('attr_'.$from,'attr_'.$to);
    }
    
    protected function changeAttributeType($id, $from, $to)
    {
        throw new \Exception('Changing the attribute type is not supported yet.');    
    }
    
    protected function changeAttributeAllowedClasses($id, $from, $to)
    {
        
    }
    
    public function editAttribute(int $id,string $name, string $type, array $allowed_classes)
    {
        if (empty($attribute = $this->query()->where('id',$id)->first())) {
            throw new InvalidAttributeIDException("The given ID '$id' is invalid");
        }
        if ($name !== $attribute->name) {
            $this->changeAttributeName($id, $attribute->name, $name);
        }
        if ($type !== $attribute->type) {
            $this->changeAttributeType($i1, $attribute->type, $type);
        }
        $allowed = Str::finish(Str::start(implode('|',$allowed_classes),'|'),'|');
        if ($allowed !== $attribute->allowed_classes) {
            $this->changeAttributeAllowedClasses($id, $attribute->allowed_classes, $allowed);
        }
        DB::table('attributes')->where('id',$id)->update(['name'=>$name,'type'=>$type,'allowed_classes'=>$allowed]);
    }
    
}
 
