<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Packages extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'package_details';	
    protected $fillable = ['activity_id','package_name','package_details','package_description','status','categoryL1','highlights'];
    protected  $primaryKey  = 'package_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
