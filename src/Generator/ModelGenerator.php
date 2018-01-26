<?php

namespace Temporaries\Document\Generator;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Temporaries\Document\Parser\ModelParser;

class ModelGenerator
{
    private $connection;

    private $schema;

    private $markdownContent;

    private $modelParser;

    private $modelPath;

    public function __construct()
    {
        $this->setConnectionConfig();
        $this->setModelPath();
    }

    public function getMarkdownContent()
    {
        return $this->markdownContent;
    }

    public function buildModel()
    {
        $this->modelParser = app(ModelParser::class);
        collect($this->modelParser->getMappedStack())->each(function ($table, $model) {
            $this->generateModel($table, $model);
        });
    }

    public function generateModel($table, $model)
    {
        $englishName = $model;
        $chineseName = '中文';
        $this->markdownContent = <<<INFO
# $englishName $chineseName

Parameter                       | Type            | Length  | Comment
:------------------------------ | :-------------- | :------ | :----------

INFO;
        $columns = $this->schema->listTableColumns($table);
        foreach ($columns as $column) {
            $this->markdownContent .=
                $this->buildMarkdownStr('parameter', $column->getName()) .
                $this->buildMarkdownStr('type', $column->getType()) .
                $this->buildMarkdownStr('length', $column->getLength()) .
                $this->buildMarkdownStr('comment', $column->getComment());
        }

        file_put_contents($this->modelPath . '/' . $model . '.md', $this->markdownContent);
    }

    private function setModelPath()
    {
        $this->modelPath = config('temporariesDoc.output.model');
        if (!is_dir($this->modelPath)) {
            mkdir($this->modelPath, 0755);
        }
    }

    private function setConnectionConfig()
    {
        $configuration = new Configuration();
        $connectionParams = config('temporariesDoc.connections');
        $this->connection = DriverManager::getConnection($connectionParams, $configuration);
        $this->schema = $this->connection->getSchemaManager();
    }

    private function buildMarkdownStr($type, $value)
    {
        if (!in_array($type, ['parameter', 'type', 'length', 'comment'])) {
            return '';
        }
        $parameterBuilder = function () use ($value) {
            return str_pad($value, 32, " ") . "|";
        };

        $typeBuilder = function () use ($value) {
            return ' ' . str_pad($value, 16, " ") . "|";
        };

        $lengthBuilder = function () use ($value) {
            return ' ' . str_pad($value, 8, " ") . "|";
        };

        $commentBuilder = function () use ($value) {
            return ' ' . $value . "\r\n";
        };

        $builder = $type . "Builder";
        return $$builder();
    }


}