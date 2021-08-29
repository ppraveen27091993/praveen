<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
	
    protected $table = 'module';

  protected $fillable=['module_code','module_name','module_term'];
}
