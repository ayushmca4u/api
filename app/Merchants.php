<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Merchants extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'merchants';	
    protected $fillable = ['email','password','name','description','address','telephone','status'];
    protected  $primaryKey  = 'id';	
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
