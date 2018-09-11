<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tag extends Model
{
    
    protected $table = 'tags';
    
    public function parent() {
    	return $this->hasOne('App\tag');
    }
    //
}
