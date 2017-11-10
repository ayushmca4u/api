<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class Banner extends Eloquent 
{

    protected $connection = 'mongodb';
    protected  $collection = 'banner_details';
    protected $primaryKey = 'banner_id';
     protected $hidden = [
       '_id',
    ];			
}
