<?php

namespace PhpSerializers\Benchmarks\Bench;

use Jane\AutoMapper\AutoMapper;
use Jane\AutoMapper\Compiler\Compiler;
use Jane\AutoMapper\Compiler\FileLoader;
use Jane\AutoMapper\Context;
use Jane\AutoMapper\Mapper;
use PhpSerializers\Benchmarks\AbstractBench;
use PhpSerializers\Benchmarks\Model\Forum;

class JaneAutomapperBenchmark extends AbstractBench
{
    /** @var Mapper */
    private $mapper;

    public function initSerializer(): void
    {
        if (!file_exists($cache = __DIR__. '/../../cache/automapper')) {
            mkdir($cache);
        }

        $loader = new FileLoader(new Compiler(), $cache);
        $automapper = AutoMapper::create(true, $loader);
        $this->mapper = $automapper->getMapper(Forum::class, 'array');
    }

    protected function serialize(Forum $data): void
    {
        $array = $this->mapper->map($data, new Context());
        json_encode($array);
    }

    public function getPackageName(): string
    {
       return 'jane-php/automapper';
    }

    public function getNote(): string
    {
        return <<<'NOTE'
Map object graphs to an array then use json_encode to serialize it
NOTE;
    }
}
