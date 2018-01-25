<?php

namespace Temporaries\Document\Console;

use Illuminate\Console\Command;

class GenerateCommand extends Command
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
     * Execute the console command.
     *
     */
    public function handle()
    {
        $file = $this->option('file');


//        $this->info('');
        return;
    }
}