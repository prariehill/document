<?php

namespace Temporaries\Document\Generator;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Temporaries\Document\Parser\ModelParser;

class ConfigGenerator
{
    public $path;

    public $files;

    public function __construct(Filesystem $filesystem,$config)
    {
        $this->files = $filesystem;
        $this->path = config_path($config);
    }

    public function build()
    {
        $parser = app(ModelParser::class);
        $models = $parser->getModelStack();
        $temporariesDoc = config('temporariesDoc');

        $configModels =  collect($temporariesDoc['relatedness'])
            ->keys();

    }

    protected function write($manifest)
    {
        if (! is_writable(dirname($this->path))) {
            throw new Exception('The '.dirname($this->path).' directory must be present and writable.');
        }

        $this->files->put(
            $this->path, '<?php return '.var_export($manifest, true).';'
        );
    }
}