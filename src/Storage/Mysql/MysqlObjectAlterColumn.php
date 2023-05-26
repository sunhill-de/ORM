<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyException;

class MysqlObjectAlterColumn extends MysqlObjectMigrateHelper
{
    
    use ColumnInfo;
    
    
    protected function checkObjectField(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
        $db_type = $this->getColumnType($this->basic_table_name, $property->getName());
        if ('integer' !== $db_type) {
            Schema::table($this->basic_table_name, function ($table) use ($property){
                $table->integer($property->getName())->change();
            });
        }
    }
    
    protected function checkVarchar(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
        if ('string' !== $this->getColumnType($this->basic_table_name, $property->getName())) {
            Schema::table($this->basic_table_name, function ($table) use ($property) {
                $table->string($property->getName(), $property->getMaxLength())->change();
            });
        }
        if ($property->getDefaultsNull()) {
            if (!$this->getColumnDefaultsNull($this->basic_table_name, $property->getName())) {
                Schema::table($this->basic_table_name, function ($table) use ($property) {
                    $table->string($property->getName(),$property->getMaxLength())->nullable()->default(null)->change();
                });
            }
            return;
        }
        if (!is_null($property->getDefault())) {
            if ($this->getColumnDefault($this->basic_table_name, $property->getName()) !== $property->getDefault()) {
                Schema::table($this->basic_table_name, function ($table) use ($property) {
                    $table->string($property->getName(),$property->getMaxLength())->default($property->getDefault())->change();
                });
            }
        }
    }
    
    protected function getTypeInDatabase(Property $property): string
    {
        return $this->getColumnType($property->getOwner()::getInfo('table'),$property->getName());    
    }
    
    protected function getHelpTableName(Property $property)
    {
        return $property->getOwner()::getInfo('table').'_'.$property->getName();
    }

    protected function isColumnNew(Property $property)
    {
        return !$this->columnExists($property->getOwner()::getInfo('table'), $property->getName());
    }

    protected function addColumnInDatabase(Property $property)
    {
        Schema::table($property->getOwner()::getInfo('table'), function ($table) use ($property) {
            $helper = new MysqlObjectAddColumn($table);
            $helper->handleProperty($property);
        });
            
    }
    
    protected function alterColumn(Property $property, $class_type)
    {
        Schema::table($property->getOwner()::getInfo('table'), function ($table) use ($property, $class_type) {
            $field = $table->$class_type($property->getName());
            if ($property->getDefaultsNull()) {
                $field->nullable()->default(null);
            } else if (!empty($property->getDefault())) {
                $field->default($property->getDefault());
            }
            $field->change();
        });
    }
    
    protected function checkChangedDefault(Property $property, string $class_type)
    {
        if ($property->getDefaultsNull()) {
            if (!$this->getColumnDefaultsNull($property->getOwner()::getInfo('table'), $property->getName())) {
                Schema::table($property->getOwner()::getInfo('table'), function ($table) use ($property, $class_type) {
                    $table->$class_type($property->getName())->nullable()->default(null)->change();
                });
            }
            return;
        }
        if (!is_null($property->getDefault())) {
            if ($this->getColumnDefault($property->getOwner()::getInfo('table'), $property->getName()) !== $property->getDefault()) {
                Schema::table($property->getOwner()::getInfo('table'), function ($table) use ($property, $class_type) {
                    $table->$class_type($property->getName())->default($property->getDefault())->change();
                });
            }
        }        
    }
    
    protected function checkChangedSearchable(Property $property)
    {
        
    }
    
    public function handlePropertyArray(Property $property)
    {
        if (!$this->tableExists($property->getOwner()::getInfo('table').'_'.$property->getName())) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            
        }
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'bool') {
                $this->alterColumn($property, 'bool');                
            } else {
                $this->checkChangedDefault($property, 'bool');
                $this->checkChangedSearchable($property);
            }
        }
    }
    
    /**
     * When a calculated field is added while altering the table the values have to be recalculated
     * @todo: not implemented yet
     */
    protected function fillCalculated()
    {
        
    }
    
    public function handlePropertyCalculated(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
            $this->fillCalculated($property);
        } else {
            if ($this->getTypeInDatabase($property) !== 'string') {
                $this->alterColumn($property, 'string');
            } else {
                $this->checkChangedSearchable($property);                
            }            
        }
    }
    
    
    public function handlePropertyDate(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'date') {
                $this->alterColumn($property, 'date');
            } else {
                $this->checkChangedDefault($property, 'date');
                $this->checkChangedSearchable($property);                
            }
            
        }
    }
    
    
    public function handlePropertyDateTime(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'datetime') {
                $this->alterColumn($property, 'datetime');
            } else {
                $this->checkChangedDefault($property, 'datetime');
                $this->checkChangedSearchable($property);                
            }            
        }
    }
    
    
    public function handlePropertyEnum(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'string') {
                $this->alterColumn($property, 'string');
            } else {
                $this->checkChangedDefault($property, 'string');
                $this->checkChangedSearchable($property);                
            }            
        }
    }
    
    
    public function handlePropertyFloat(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'float') {
                $this->alterColumn($property, 'float');
            } else {
                $this->checkChangedDefault($property, 'float');
                $this->checkChangedSearchable($property);                
            }            
        }
    }
    
    
    public function handlePropertyInteger(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'integer') {
                $this->alterColumn($property, 'integer');
            } else {
                $this->checkChangedDefault($property, 'integer');
                $this->checkChangedSearchable($property);
            }
            
        }
    }
    
    
    public function handlePropertyMap(Property $property)
    {
        if (!$this->tableExists($property->getOwner()::getInfo('table').'_'.$property->getName())) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            
        }        
    }
    
    
    public function handlePropertyObject(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'integer') {
                $this->alterColumn($property, 'integer');
            } else {
                $this->checkChangedDefault($property, 'integer');
                $this->checkChangedSearchable($property);
            }            
        }
    }
    
    
    public function handlePropertyTags(Property $property)
    {
        
    }
    
    
    public function handlePropertyText(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'text') {
                $this->alterColumn($property, 'text');
            } else {
                $this->checkChangedDefault($property, 'default');
            }            
        }
    }
    
    
    public function handlePropertyTime(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'time') {
                $this->alterColumn($property, 'time');
            } else {
                $this->checkChangedDefault($property, 'time');
                $this->checkChangedSearchable($property);
            }            
        }
    }
    
    
    public function handlePropertyTimestamp(Property $property)
    {
        
    }
    
    protected function checkChangedMaxlen(Property $property)
    {
        
    }
    
    public function handlePropertyVarchar(Property $property)
    {
        if ($this->isColumnNew($property)) {
            $this->addColumnInDatabase($property);
        } else {
            // The column is not new. The type could have changed
            if ($this->getTypeInDatabase($property) !== 'string') {
                $this->alterColumn($property, 'string');   
            } else {
                $this->checkChangedMaxlen($property);
                $this->checkChangedDefault($property, 'string');
                $this->checkChangedSearchable($property);
            }
        }
    }
    
}