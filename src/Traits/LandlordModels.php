<?php

namespace HenryAvila\LaravelNovaMultitenancy\Traits;

trait LandlordModels
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection(config('database.default'));
    }
}
