<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class stringobjectassign extends Model
{
	
	protected $table = 'stringobjectassigns';
	
	protected $fillable = ['container_id','element_id'];
	
	public $timestamps = false;
}
