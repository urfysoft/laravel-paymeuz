<?php

namespace Urfysoft\Payme\Commands;

use Illuminate\Console\Command;

class PaymeCommand extends Command
{
    public $signature = 'laravel-paymeuz';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
