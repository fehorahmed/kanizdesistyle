<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Compare extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'compare'; // binding key for CompareService
    }
}
