<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tagobjectassign extends Model
{
	
	protected $table = 'tagobjectassigns';
	
	protected $fillable = ['tag_id','object_id'];
	
	public $timestamps = false;
}
