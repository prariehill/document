<?php

namespace Temporaries\Document;


use Temporaries\Document\Generator\ModelGenerator;
use Temporaries\Document\Parser\ModelParser;

class Manager
{
    protected $modelParser;

    protected $modelGenerator;

    protected $modelMappedStack;

    public function __construct(ModelParser $modelParser,
                                ModelGenerator $modelGenerator)
    {
        $this->modelParser = $modelParser;
        $this->modelGenerator = $modelGenerator;
        $this->modelMappedStack = $modelParser->getMappedStack();
    }

    public function getMappedStack()
    {
        return $this->modelMappedStack;
    }
}