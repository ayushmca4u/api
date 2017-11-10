<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Paymenttransactions extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $table = 'payment_transaction_details';	
    protected $fillable = ['gtdid','gid','orderid','merchanttxnrefid','currency','amount'];
    protected  $primaryKey  = 'gtdid';	

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
