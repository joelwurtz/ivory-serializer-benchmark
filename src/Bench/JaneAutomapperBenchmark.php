<?php

namespace PhpSerializers\Benchmarks\Bench;

use Doctrine\Common\Annotations\AnnotationReader;
use Jane\AutoMapper\AutoMapper;
use Jane\AutoMapper\Compiler\Accessor\ReflectionAccessorExtractor;
use Jane\AutoMapper\Compiler\Compiler;
use Jane\AutoMapper\Compiler\FileLoader;
use Jane\AutoMapper\Compiler\FromSourcePropertiesMappingExtractor;
use Jane\AutoMapper\Compiler\FromTargetPropertiesMappingExtractor;
use Jane\AutoMapper\Compiler\SourceTargetPropertiesMappingExtractor;
use Jane\AutoMapper\Compiler\Transformer\ArrayTransformerFactory;
use Jane\AutoMapper\Compiler\Transformer\BuiltinTransformerFactory;
use Jane\AutoMapper\Compiler\Transformer\ChainTransformerFactory;
use Jane\AutoMapper\Compiler\Transformer\MultipleTransformerFactory;
use Jane\AutoMapper\Compiler\Transformer\NullableTransformerFactory;
use Jane\AutoMapper\Compiler\Transformer\ObjectTransformerFactory;
use Jane\AutoMapper\Context;
use Jane\AutoMapper\Mapper;
use Jane\AutoMapper\MapperConfiguration;
use Jane\AutoMapper\MapperConfigurationFactory;
use PhpSerializers\Benchmarks\AbstractBench;
use PhpSerializers\Benchmarks\Model\Forum;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class JaneAutomapperBenchmark extends AbstractBench
{
    /** @var Mapper */
    private $mapper;

    public function initSerializer(): void
    {
        if (!file_exists($cache = __DIR__. '/../../cache/automapper')) {
            mkdir($cache);
        }
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();
        $transformerFactory = new ChainTransformerFactory();

        $sourceTargetMappingExtractor = new SourceTargetPropertiesMappingExtractor(new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$reflectionExtractor, $phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        ),
            new ReflectionAccessorExtractor(),
            $transformerFactory,
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()))
        );

        $fromTargetMappingExtractor = new FromTargetPropertiesMappingExtractor(new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$reflectionExtractor, $phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        ),
            new ReflectionAccessorExtractor(),
            $transformerFactory,
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()))
        );

        $fromSourceMappingExtractor = new FromSourcePropertiesMappingExtractor(new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$reflectionExtractor, $phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        ),
            new ReflectionAccessorExtractor(),
            $transformerFactory,
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()))
        );

        $automapper = new AutoMapper(new FileLoader(new Compiler(), $cache), new MapperConfigurationFactory(
            $sourceTargetMappingExtractor,
            $fromSourceMappingExtractor,
            $fromTargetMappingExtractor
        ));

        $transformerFactory->addTransformerFactory(new MultipleTransformerFactory($transformerFactory));
        $transformerFactory->addTransformerFactory(new NullableTransformerFactory($transformerFactory));
        $transformerFactory->addTransformerFactory(new BuiltinTransformerFactory());
        $transformerFactory->addTransformerFactory(new ArrayTransformerFactory($transformerFactory));
        $transformerFactory->addTransformerFactory(new ObjectTransformerFactory($automapper));

        $configurationForum = new MapperConfiguration($fromSourceMappingExtractor, Forum::class, 'array');
        $automapper->register($configurationForum);
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
