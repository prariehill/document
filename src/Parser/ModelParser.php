<?php

namespace Temporaries\Document\Parser;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;

class ModelParser
{
    public $mappedStack = [];

    public $modelStack = [];

    public $tableStack = [];

    public $relatedStack = [];

    public $nicknameStack = [];

    private $namespace;

    private $schema;

    private $filesystem;

    private $path;

    public function __construct()
    {
        $this->path = config('document.input.path');
        $this->namespace = config('document.input.namespace');
        $this->filesystem = app(Filesystem::class);
        $this->setConnectionConfig();
        $this->buildMap();
    }

    public function getOutputPath($model)
    {
        if (!is_dir($directory = config('document.output.model'))) {
            $this->filesystem->makeDirectory($directory);
        }
        return $directory . DIRECTORY_SEPARATOR . $model . ".md";
    }

    public function getMarkdown($model)
    {
        $chineseName = $this->nicknameStack[$model] ?? '';
        $content = <<<INFO
# $model $chineseName

Parameter                       | Type            | Length  | Comment
:------------------------------ | :-------------- | :------ | :----------

INFO;
        $columns = $this->schema->listTableColumns($this->mappedStack[$model]);
        foreach ($columns as $column) {
            $type = $column->getType() == 'Boolean' ? 'Integer' : $column->getType();

            $content .=
                $this->buildParameter($column->getName()) .
                $this->buildType($type) .
                $this->buildLength($column->getLength()) .
                $this->buildComment($column->getComment());
        }

        if (isset($this->relatedStack[$model])) {
            foreach ($this->relatedStack[$model] as $model) {
                $content .=
                    $this->buildParameter("[$model]($model.md)") .
                    $this->buildType('Object') .
                    $this->buildLength('') .
                    $this->buildComment($this->nicknameStack[$model] ?? '');
            }
        }
        return $content;
    }


    protected function buildMap()
    {
        $files = $this->filesystem->files($this->path);

        collect($files)->each(function ($file) {
            if ($model = $this->matchModel($file->getFilename())) {
                $this->modelStack[] = $model;
                if ($nickname = $this->matchNickname($file->getContents()))
                    $this->nicknameStack[$model] = $nickname;
                if ($related = $this->matchRelated($file->getContents()))
                    $this->relatedStack[$model] = $related;
            }
        });

        collect($this->modelStack)->each(function ($model) {
            $table = $this->getTable($model);
            if ($table) {
                $this->tableStack[] = $table;
                $this->mappedStack[$model] = $table;
            }
        });
    }

    protected function getTable($model)
    {
        $class = $this->namespace . $model;
        $reflection = new \ReflectionClass($class);
        return $reflection->isSubclassOf(Model::class) ? app($class)->getTable() : '';
    }

    protected function matchModel($content)
    {
        return preg_match('/(.*)\.php/', $content, $matches) ? $matches[1] : '';
    }

    protected function matchNickname($content)
    {
        return preg_match("/@DocName=(.+)" . PHP_EOL . "/", $content, $matches) ? $matches[1] : '';
    }

    protected function matchRelated($content)
    {
        return preg_match("/@DocRelated=(.+)" . PHP_EOL . "/", $content, $matches) ?
            collect(explode(',', $matches[1]))->map(function ($related) {
                return trim($related);
            })->filter()->toArray()
            : [];
    }

    private function setConnectionConfig()
    {
        $configuration = new Configuration();
        $connectionParams = config('document.connections');
        $this->schema = DriverManager::getConnection($connectionParams, $configuration)->getSchemaManager();
    }

    protected function buildParameter($value)
    {
        return str_pad($value, 32, " ") . "|";
    }

    protected function buildType($value)
    {
        return ' ' . str_pad($value, 16, " ") . "|";
    }

    protected function buildLength($value)
    {
        return ' ' . str_pad($value, 8, " ") . "|";
    }

    protected function buildComment($value)
    {
        return ' ' . $value . "\r\n";
    }
}