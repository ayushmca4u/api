<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Activities extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'activity_details';	
    protected $fillable = ['neighborhood','address','address','venue_location'];
    protected  $primaryKey  = 'activity_id';	
    protected $hidden = [
       'password',
    ];	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
