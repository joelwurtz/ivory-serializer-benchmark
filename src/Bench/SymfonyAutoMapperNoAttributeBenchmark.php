<?php

namespace PhpSerializers\Benchmarks\Bench;

use PhpSerializers\Benchmarks\AbstractBench;
use PhpSerializers\Benchmarks\Model\Forum;
use Symfony\Component\AutoMapper\AutoMapper;
use Symfony\Component\AutoMapper\AutoMapperNormalizer;
use Symfony\Component\AutoMapper\Generator\Generator;
use Symfony\Component\AutoMapper\Loader\FileLoader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class SymfonyAutoMapperNoAttributeBenchmark extends AbstractBench
{
    private $serializer;

    public function initSerializer(): void
    {
        $directory = __DIR__. '/../../cache/symfony-automapper-noattrs';

        if (!file_exists($directory)) {
            @mkdir($directory, 0777, true);
        }

        $loader = new FileLoader(
            new Generator(),
            $directory
        );

        $automapper = AutoMapper::create(true, $loader, null, 'Mapper_', false);

        $this->serializer = new Serializer(
            [new AutoMapperNormalizer($automapper)],
            [new JsonEncoder()]
        );
    }

    protected function serialize(Forum $data): void
    {
        $this->serializer->serialize($data, 'json');
    }

    public function getPackageName(): string
    {
       return 'symfony/auto-mapper';
    }

    public function getNote(): string
    {
        return <<<'NOTE'
Map object graphs to an array then use json_encode to serialize it, do not use attribute checking
NOTE;
    }
}
