<?php

namespace Statix\FormAction\Commands;

use Illuminate\Console\Command;

class CreateFormActionCommand extends Command
{
    public $signature = 'make:form-action {name}';

    public $description = 'Create a new form action';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
