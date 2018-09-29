<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class objectobjectassign extends Model
{
	
	protected $table = 'objectobjectassigns';
	
	protected $fillable = ['container_id','element_id'];
	
	public $timestamps = false;
}
