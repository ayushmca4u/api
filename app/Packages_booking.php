<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Packages_booking extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'package_booking_details';	
    protected $fillable = ['package_id','type','best_discount','start_latitude','start_longitude','end_latitude','end_latitude'];
    protected  $primaryKey  = 'package_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
