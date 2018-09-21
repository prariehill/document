<?php

namespace Temporaries\Document\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Temporaries\Document\Generator\ModelGenerator;
use Temporaries\Document\Manager;
use Temporaries\Document\Parser\ModelParser;

class DocumentGeneratorCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:generate';
//            {file} {--f|force} {--o|overwrite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate document';

    protected $modelGenerator;

    public function __construct(Filesystem $files,ModelGenerator $modelGenerator)
    {
        parent::__construct($files);
        $this->modelGenerator = $modelGenerator;
    }

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
        $this->modelGenerator->generate();
        $this->info('success');
//        $manager = app(Manager::class);
//        $manager->getMappedStack()->each(function ($table, $model) {
//            var_dump(1);
//            $this->info($table . ':' . $model);
//        });
//
//        $file = $this->argument('file');
//        $this->info($file);
//        $force = $this->option('force');
//        $overwrite = $this->option('overwrite');
//        $this->info($force);
//        $this->info($overwrite);
        return;
    }
}