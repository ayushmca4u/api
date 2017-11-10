<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class test extends Eloquent 
{

    protected $connection = 'mongodb';
    protected  $collection = 'test';	
}
