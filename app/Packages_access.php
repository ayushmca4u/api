<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Packages_access extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'package_access_details';	
    protected $fillable = ['package_id','identification_card','identification_val','allowed_member','allowed_age_group'];
    protected  $primaryKey  = 'package_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
