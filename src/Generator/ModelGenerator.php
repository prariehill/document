<?php

namespace Temporaries\Document\Generator;

use Illuminate\Filesystem\Filesystem;
use Temporaries\Document\Parser\ModelParser;

class ModelGenerator
{
    private $parser;

    private $filesystem;

    public function __construct(ModelParser $parser,Filesystem $filesystem)
    {
        $this->parser = $parser;
        $this->filesystem = $filesystem;
    }

    public function generate()
    {
        collect($this->parser->stack)->each(function ($model) {
            $this->filesystem->put($this->parser->getModelOutputPath($model), $this->parser->getMarkdown($model));
        });
    }
}