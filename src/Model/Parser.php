<?php

namespace Temporaries\Document\Model;

class Parser
{
    private $mappedStack = [];

    private $modelStack = [];

    private $tableStack = [];

    private $namespace;

    private $modelPath;

    public function __construct($namespace = null, $modelPath = null)
    {
        $this->setNamespace($namespace);
        $this->setPath($modelPath);
        $this->generateMap();
    }

    private function generateMap()
    {
        $DirectoryIterator = new \DirectoryIterator($this->modelPath);

        foreach ($DirectoryIterator as $file) {
            if (preg_match('/(.*)\.php/', $file->getFilename(), $matchs)) {
                $this->modelStack[] = $matchs[1];
            }
        };

        collect($this->modelStack)->each(function ($model) {
            $table = $this->getTable($model);
            if (!is_null($table)) {
                $this->tableStack[] = $table;
                $this->mappedStack[$model] = $table;
            }
        });
    }

    private function getTable($model)
    {
        $class = $this->namespace . $model;
        try {
            return app($class)->getTable();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function setNamespace($namespace)
    {
        $this->namespace = $namespace ??
            'App\\';
    }

    private function setPath($path = null)
    {
        if (is_null($path)) {
            return $this->modelPath = $this->convertNamespaceIntoPath();
        }

        if (is_dir($processedPath = base_path($path))) {
            return $this->modelPath = $processedPath;
        }

        if (is_dir($path)) {
            return $this->modelPath = $path;
        }

        if (!is_dir($this->modelPath)) {
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