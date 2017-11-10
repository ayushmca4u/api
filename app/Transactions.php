<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Transactions extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'transactions';	
    protected $fillable = ['order_id','shopper_id','package_id','activity_id','merchant_id','web_price','list_price','cost_price','currency','quantity','booking_activity_date','booking_activity_time'];
    protected  $primaryKey  = 'transaction_id';	
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
