<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class Cities extends Eloquent 
{

    protected $connection = 'mongodb';
    protected  $collection = 'cities';
    protected $primaryKey = 'city_id';
     protected $hidden = [
       '_id',
    ];			
}
