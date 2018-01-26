<?php

namespace Temporaries\Document\Parser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;

class ModelParser
{
    protected $mappedStack = [];

    protected $modelStack = [];

    protected $tableStack = [];

    private $namespace;

    private $filesystem;

    private $path;

    public function __construct()
    {
        $this->path = base_path(config('temporariesDoc.input.path'));
        $this->namespace = config('temporariesDoc.input.namespace');
        $this->filesystem = app(Filesystem::class);
        $this->buildMap();
    }

    public function getMappedStack()
    {
        return collect($this->mappedStack);
    }

    protected function buildMap()
    {
        $files = $this->filesystem->files($this->path);

        foreach ($files as $file) {
            if (preg_match('/(.*)\.php/', $file->getFilename(), $matchs)) {
                $this->modelStack[] = $matchs[1];
            }
        };

        foreach ($this->modelStack as $model) {
            $table = $this->getTable($model);
            if (!is_null($table)) {
                $this->tableStack[] = $table;
                $this->mappedStack[$model] = $table;
            }
        }
    }

    protected function getTable($model)
    {
        $class = $this->namespace . $model;
        $reflection = new \ReflectionClass($class);
        return $reflection->isSubclassOf(Model::class) ? app($class)->getTable() : null;
    }
}