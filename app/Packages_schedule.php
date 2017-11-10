<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Packages_schedule extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'package_schedule_details';	
    protected $fillable = ['package_id','activity_id','activity_startdate','activity_enddate','status','activity_type','start_day','end_day','start_time','endtime'];
    protected  $primaryKey  = 'package_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
