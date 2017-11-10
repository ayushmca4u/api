<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class Countries extends Eloquent 
{

    protected $connection = 'mongodb';
    protected  $collection = 'countries';
    protected $primaryKey = 'country_id';
    protected $hidden = [
       '_id',
    ];			
}
