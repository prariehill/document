<?php

namespace Temporaries\Document\Console;

use Illuminate\Console\GeneratorCommand;

class GenerateCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:generate
            {file} {--F|force} {--O|overwrite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate document';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $file = $this->option('file');
        $this->info($file);
        return;
    }
}