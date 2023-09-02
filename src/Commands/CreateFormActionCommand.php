<?php

namespace Statix\FormAction\Commands;

use Illuminate\Console\Command;

class CreateFormActionCommand extends Command
{
    public $signature = 'make:form-action {name?} {--namespace="App\\FormActions"}';

    public $description = 'Create a new form action';

    public function handle(): int
    {
        $name = $this->determineName();

        $namespace = $this->determineNamespace();

        $stubContents = file_get_contents($this->getStub());

        $stubContents = str_replace('{{ CLASS_NAME }}', $name, $stubContents);

        $stubContents = str_replace('{{ NAMESPACE }}', $namespace, $stubContents);

        $outputPath = base_path("app/FormActions/{$name}.php");

        file_put_contents($outputPath, $stubContents);

        $this->info("Form action {$name} created successfully");

        $this->info($outputPath);

        return self::SUCCESS;
    }

    protected function determineName(): string
    {
        // if the name is already set, return it
        if ($this->argument('name')) {
            return str($this->argument('name'))->studly()->trim();
        }

        $name = $this->ask('What should the form action be called');

        if (! $name) {
            $this->error('A name is required');

            return $this->determineName();
        }

        return str($name)->studly()->trim();
    }

    protected function determineNamespace(): string
    {
        // if the namespace is already set, return it
        if ($this->option('namespace')) {
            return str($this->option('namespace'))->studly()->trim();
        }

        return str('App\\FormActions')->studly()->trim();
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/FormAction.stub';
    }
}
