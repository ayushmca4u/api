<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Merchant_voucher extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'merchant_voucher_details';	
    protected $fillable = ['package_id','merchant_id','booking_id','voucher_code','ttd_voucher_code'];
    protected  $primaryKey  = 'vid';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
