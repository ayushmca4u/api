<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Categories extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'categories';	
    protected $fillable = ['cg_name','cg_l1','cg_l2','cg_l3','cg_isleaf','status','cg_desc','cg_imagename','cg_breadcrum'];
    protected  $primaryKey  = 'cg_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
