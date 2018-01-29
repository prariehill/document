<?php

namespace Temporaries\Document\Parser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class ModelParser
{
    private $schema;

    private $filesystem;


    private $namespace;

    private $inputPath;

    private $outputPath = [];

    public $stack = [];

    public $aliasStack = [];

    public $relatedStack = [];

    public $mappedStack = [];

    private $defaultAliasStack = [];

    private $defaultRelatedStack = [];

    private $defaultColumnCommentStack = [
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间'
    ];


    public function __construct()
    {
        $this->schema = DB::getDoctrineSchemaManager();
        $this->filesystem = app(Filesystem::class);

        $this->inputPath = config('document.input.path');
        $this->namespace = config('document.input.namespace');
        $this->outputPath = config('document.output.path');
        $this->defaultColumnCommentStack = config('document.default.column');
        $this->defaultAliasStack = config('document.default.model');
        $this->build();
    }

    public function getModelOutputPath($model)
    {
        if (!is_dir($directory = $this->outputPath['model'])) {
            $this->filesystem->makeDirectory($directory);
        }
        return $directory . DIRECTORY_SEPARATOR . $model . ".md";
    }

    public function getMarkdown($model)
    {
        $chineseName = $this->getModelAlias($model);
        $content = <<<INFO
# $model $chineseName

Parameter                       | Type            | Length  | Comment
:------------------------------ | :-------------- | :------ | :----------

INFO;

        $columns = $this->schema->listTableColumns($this->mappedStack[$model]);

        foreach ($columns as $column) {
            $type = $column->getType() == 'Boolean' ? 'Integer' : $column->getType();
            $comment = $column->getComment() ?? ($this->defaultColumnComments[$column->getName()] ?? '');
            $content .=
                $this->buildParameter($column->getName()) .
                $this->buildType($type) .
                $this->buildLength($column->getLength()) .
                $this->buildComment($comment);
        }

        if (isset($this->relatedStack[$model])) {
            foreach ($this->relatedStack[$model] as $model) {
                $content .=
                    $this->buildParameter("[$model]($model.md)") .
                    $this->buildType('Object') .
                    $this->buildLength('') .
                    $this->buildComment($this->nicknameStack[$model] ?? ($this->defaultModelComments[$model] ?? ''));
            }
        }
        return $content;
    }


    protected function build()
    {
        $files = $this->filesystem->files($this->inputPath);

        foreach ($files as $file) {
            if ($model = $this->matchModel($file->getFilename())) {

                $this->stack[] = $model;

                $this->aliasStack[$model] = $this->matchAlias($file->getContents()) ?? $this->defaultAliasStack[$model] ?? '';

                $this->relatedStack[$model] = $this->matchRelated($file->getContents()) ?? $this->processRelated($this->defaultRelatedStack[$model] ?? '');
            }
        }

        foreach ($this->stack as $model) {
            $table = $this->getTable($model);
            if ($table) $this->mappedStack[$model] = $table;
        }
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

    protected function matchAlias($content)
    {
        return preg_match("/\/\*\*[^\f\t\v]+@TDocName=(.+)[^\f\t\v]+\*\//", $content, $matches) ? $matches[1] : '';
    }

    protected function matchRelated($content)
    {
        return preg_match("/\/\*\*[^\f\t\v]+@TDocRelated=(.+)[^\f\t\v]+\*\//", $content, $matches) ?
            $this->processRelated($matches[1])
            : [];
    }

    protected function processRelated($related)
    {
        return collect(explode(',', $related))->map(function ($model) {
            return trim($model);
        })->filter()->sort()->toArray();
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

    private function getModelAlias($model)
    {
        return $this->modelAliasStack[$model] ?? ($this->defaultModelAlias[$model] ?? '');
    }
}