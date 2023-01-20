<?php

namespace HenryAvila\LaravelNovaMultitenancy\Commands;

use Illuminate\Console\Command;

class LaravelNovaMultitenancyCommand extends Command
{
    public $signature = 'laravel-nova-multitenancy';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
