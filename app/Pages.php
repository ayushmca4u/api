<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Pages extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'pages';	
    protected $fillable = ['page_title','page_url','page_content','redirect_url','meta_content','status','meta_keyword','meta_title','meta_description'];
    protected  $primaryKey  = 'page_id';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
