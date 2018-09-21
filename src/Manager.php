<?php

namespace Temporaries\Document;

use Temporaries\Document\Generator\ModelGenerator;
use Temporaries\Document\Parser\ModelParser;

class Manager
{
    protected $modelParser;

    protected $modelGenerator;

    protected $modelMappedStack;

    public function __construct(ModelParser $modelParser)
    {
        $this->modelParser = $modelParser;
        $this->modelMappedStack = $modelParser->getMappedStack();
    }

    public function getMappedStack()
    {
        return $this->modelMappedStack;
    }
}