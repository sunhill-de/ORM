<?php

/**
 * @file ClassManager.php
 * Provides the ClassManager object for accessing information about the orm classes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-23
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Illuminate\Support\Facades\Lang;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Utils\ObjectMigrator;
use Sunhill\ORM\Storage\StorageBase;
use Doctrine\Common\Lexer\Token;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Managers\Exceptions\ClassNotAccessibleException;
use Sunhill\ORM\Managers\Exceptions\ClassNotORMException;
use Sunhill\ORM\Managers\Exceptions\ClassNameForbiddenException;
use Sunhill\ORM\Managers\ClassQuery\ClassQuery;

 /**
  * Wrapper class for handling of objectclasses. It provides some public static methods to get informations about
  * the installed objectclasses. At this time there is no kind of registration of new objectclasses, they are just put
  * in one (or perhaps more) directory. Therefore the warpper needs to access this (these) directory and read 
  * out the single objectclasses. 
  * The problem is that objectclasses a called by namespace and autoloader and there is no sufficient method to 
  * get the installed objectclasses at the momement, so we have to read out the specific directories.
  * Definition of objectclass:
  * A descendand of Sunhill\ORM\Objects\ORMObject which represents a storable dataobject
  * @author lokal
  * Test: Unit/Managers/ManagerClassesTest.php
  */
class ClassManager extends PropertiesCollectionManager
{
 
    static protected $base_class = ORMObject::class;
    
    
// ********************************** Register class ******************************************
        
    
    /**
     * Returns the parent entry of this class
     * 
     * @param array $result The array to store the information to
     * @param string $class The full namespace of the class (not the class name!)
     * 
     * Test: testGetClassParentEntry
     */
    protected function buildAdditionalInformation(\StdClass $result,string $class): void 
    {
        $parent = get_parent_class($class);
        if ($class !== ORMObject::class) { 
            $result->parent = $parent::getInfo('name');
        } else {
            $result->parent = '';            
        }
    }
       
     /**
      * Checks if the given classpath is a descendant of ORMObject
      * @param string $classpath
      * @throws ClassNotORMException
      */
     protected function checkClassType(string $classpath)
     {
         if (!is_a($classpath, ORMObject::class,true)) {
             throw new ClassNotORMException("The class '$classpath' is not a descendant of ORMObject");
         }
     }

    /**
     * Every single class that should be accessible via the class manager should be added through this method. 
     * It is possible to use an ORMObject without registering even store it but all references to other
     * classes (like PropertyObject) is performed via the classname. Therefore yout should register it.
     *  
     * @param $classname string The fully qualified name of the class to register
     * @return bool true if successful false if not
     * @throws ClassNotAccessibleException::class when the class is not found
     * @throws ClassNameForbiddenException::class when the class name is invalid
     * @throws ClassNotORMException::class when the class is not a descendant of ORMObject
     * 
     * Test: testRegisterClass*
     */
    public function registerClass(string $classname, bool $ignore_duplicate = false) 
    {
        $this->register($classname, $ignore_duplicate);
    }
    
    protected function initialize()
    {
        $this->registered_items = [
        'object'=>$this->getItemInformation(ORMObject::class)
        ];
    }
        
// *************************** General class informations ===============================    
    /**
     * Returns the number of registered classes
     * 
     * @return int the number of registered Classes
     * 
     * Test: testNumberOfClasses
     */
    public function getClassCount(): int 
    {

        return count($this->registered_items);       
    }
    
    /**
     * Returns a treversable associative array of all registered classes
     * 
     * @return array the information of all registered classes
     * 
     * Test: testGetAllClasses
     */
    public function getAllClasses(): array 
    {
        
        return $this->registered_items;        
    }
    
    /**
     * Returns an array with the root ORMObject. Each entry is an array with the name of the
     * class as key and its children as another array.
     * Example:
     * ['object'=>['parent_object'=>['child1'=>[],'child2'=[]],'another_parent'=>[]]
     * 
     * Test: testGetClassTree
     */
    public function getClassTree(string $class = 'object') 
    {
        return [$class=>$this->getChildrenOfClass($class)];
    }
    
    // *************************** Informations about a specific class **************************    
    
    /**
     * Returns the inheritance of the given class.
     * 
     * @param $class The class to get the parent of
     * @param bool $include_self
     * 
     * Test: testGetInheritance
     */
    public function getInheritanceOfClass($class,bool $include_self = false) 
    {
        $class = $this->checkClass($class);

        if ($include_self) {
            $result = [$class];
        } else {
            $result = [];
        }
        
        do {
            $class = $this->getParentOfClass($class);
            $result[] = $class;
        } while ($class !== 'object');
        
        return $result;
    }
       
    /**
     * Return an associative array of the children of the passed class. The array is in the form
     *  name_of_child=>[list_of_children_of_this_child]
     *  
     * @param string $class Name of the class to which all children should be searched. Default=object
     * @param int $level search children only to this depth. -1 means search all children. Default=-1
     * 
     * Test: testGetChildrenOfClass
     */
    public function getChildrenOfClass($class='object',int $level=-1) : array 
    {
        $class = $this->checkClass($class);
        
        $result = [];
        if (!$level) { // We reached top level
            return $result;
        }
        foreach ($this->registered_items as $class_name => $info) {
            if ($info->parent === $class) {
                $result[$class_name] = $this->getChildrenOfClass($class_name,$level-1);
            }
        }
        return $result;
    }
        
    /**
     * Returns an array of all used tables of this class
     * 
     * @param string $class
     * 
     * Test: testGetUsedTablesOfClass
     */
    public function getUsedTablesOfClass(string $class)
    {
        $inheritance = $this->getInheritanceOfClass($class, true);
        
        $result = [];
        
        foreach ($inheritance as $ancestor) {
            $result[] = $this->getTableOfClass($ancestor);    
        }
        
        return $result;
    }
    
    /**
     * Creates an instance of the passes class
     * 
     * @param string $class is either the namespace or the class name
     * @return ORMObject The created instance of $class
     * 
     * Test: testCreateObject
     */
    public function createObject($class) 
    {
        $classspace = $this->getNamespaceOfClass($class);

        return new $classspace();        
    }
       
    /**
     * The reimplementation of is_a() that works with class names too
     * 
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     * 
     * Test: testIsA
     */
    public function isA($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($class);
        
        return is_a($test,$namespace, true);
    }
    
    /**
     * Returns true is $test is exactly a $class and not of its children
     * 
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     * 
     * Test: isAClass
     */
    public function isAClass($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        return is_a($test,$namespace, true) && !is_subclass_of($test,$namespace);
    }
   
    /**
     * Naming convention compatible method
     * The reimplementation of is_subclass_of() that works with class names too
     * 
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     * 
     * Test: isSubclassOf
     */
    public function isSubclassOf($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        $test_space = $this->getNamespaceOfClass($this->checkClass($this->searchClass($test)));
        return is_subclass_of($test_space,$namespace);        
    }
    
    /**
     * Creates the necessary tables for this class and checks if the fields are up to date
     */
    public function migrateClass(string $class_name) 
    {
        $class_namespace = $this->getNamespaceOfClass($class_name);
        $class_namespace::migrate();
    }
    
    public function migrateClasses()
    {
        $classes = $this->getAllClasses();
        if (!empty($classes)) {
            foreach($classes as $name => $infos) {
                $this->migrateClass($name);
            }
        }        
    }
    
    public function query(): BasicQuery
    {
        return new ClassQuery();
    }
}
