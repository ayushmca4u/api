<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Packages_price extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'package_price_details';	
    protected $fillable = ['package_id','list_price','web_price','cost_price','free_allowed','vat_required'];
    protected  $primaryKey  = 'package_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
