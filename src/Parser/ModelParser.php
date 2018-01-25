<?php

namespace Temporaries\Document\Parser;

use Illuminate\Filesystem\Filesystem;

class ModelParser
{
    protected $mappedStack = [];

    protected $modelStack = [];

    protected $tableStack = [];

    protected $namespace;

    protected $path;

    protected $filesystem;

    public function __construct(Filesystem $filesystem, $namespace = null, $path = null)
    {
        $this->filesystem = $filesystem;
        $this->setNamespace($namespace);
        $this->setPath($path);
        $this->buildStack();
    }

    protected function buildStack()
    {
        $this->buildModelStack();
        $this->buildMappedStack();
    }

    protected function buildModelStack()
    {
        $files = $this->filesystem->files($this->path);

        foreach ($files as $file) {
            if (preg_match('/(.*)\.php/', $file->getFilename(), $matchs)) {
                $this->modelStack[] = $matchs[1];
            }
        };
    }

    protected function buildMappedStack()
    {
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
        try {
            return app($class)->getTable();
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace ?? 'App\\';
    }

    protected function setPath($path = null)
    {
        if (is_null($path)) {
            return $this->path = $this->convertNamespaceIntoPath();
        }

        if (is_dir($path)) {
            return $this->path = $path;
        }

        if (is_dir($processedPath = base_path($path))) {
            return $this->path = $processedPath;
        }

        if (!is_dir($this->path)) {
            throw new \Exception('Invalid directory');
        }

    }

    private function convertNamespaceIntoPath()
    {
        $slashConversion = str_replace('\\', DIRECTORY_SEPARATOR, $this->namespace);
        $relativePath = lcfirst($slashConversion);
        $processedPath = base_path($relativePath);
        return $processedPath;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}