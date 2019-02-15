<?php

namespace PhpSerializers\Benchmarks\Bench;

use PhpSerializers\Benchmarks\AbstractBench;
use PhpSerializers\Benchmarks\Model\Forum;
use Symfony\Component\AutoMapper\AutoMapper;
use Symfony\Component\AutoMapper\AutoMapperNormalizer;
use Symfony\Component\AutoMapper\Context;
use Symfony\Component\AutoMapper\Generator\Generator;
use Symfony\Component\AutoMapper\Loader\FileLoader;
use Symfony\Component\AutoMapper\MapperInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class SymfonyAutoMapperBenchmark extends AbstractBench
{
    /** @var MapperInterface */
    private $serializer;

    public function initSerializer(): void
    {
        if (!file_exists($cache = __DIR__. '/../../cache/symfony-automapper')) {
            mkdir($cache);
        }

        $loader = new FileLoader(
            new Generator(),
            __DIR__. '/../../cache/symfony-automapper'
        );

        $this->serializer = new Serializer(
            [new AutoMapperNormalizer( AutoMapper::create(false, $loader))],
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
Map object graphs to an array then use json_encode to serialize it
NOTE;
    }
}