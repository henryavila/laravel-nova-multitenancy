<?php

namespace HenryAvila\LaravelNovaMultitenancy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HenryAvila\LaravelNovaMultitenancy\LaravelNovaMultitenancy
 */
class LaravelNovaMultitenancy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \HenryAvila\LaravelNovaMultitenancy\LaravelNovaMultitenancy::class;
    }
}
