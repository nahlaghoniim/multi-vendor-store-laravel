<?php
namespace App\Facades;
use Facade;

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Repositories\Cart\CartRepository';
    }
}