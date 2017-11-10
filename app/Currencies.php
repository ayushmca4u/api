<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class Currencies extends Eloquent 
{

    protected $connection = 'mongodb';
    protected  $collection = 'currencies';
    protected $primaryKey = 'currency_id';
     protected $hidden = [
       '_id',
    ];			
}
