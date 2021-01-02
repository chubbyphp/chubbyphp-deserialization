<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\DependencyInjection;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\DependencyInjection\DeserializationCompilerPass;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \Chubbyphp\Deserialization\DependencyInjection\DeserializationCompilerPass
 *
 * @internal
 */
final class DeserializationCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $stdClassMapping = $this->getStdClassMapping();
        $stdClassMappingClass = get_class($stdClassMapping);

        $container = new ContainerBuilder();
        $container->addCompilerPass(new DeserializationCompilerPass());

        $container
            ->register('chubbyphp.deserializer.decoder.type.json', JsonTypeDecoder::class)
            ->addTag('chubbyphp.deserializer.decoder.type')
        ;

        $container
            ->register('stdclass', $stdClassMappingClass)
            ->addTag('chubbyphp.deserializer.denormalizer.objectmapping')
        ;

        $container->compile();

        self::assertTrue($container->has('chubbyphp.deserializer'));
        self::assertTrue($container->has('chubbyphp.deserializer.decoder'));
        self::assertTrue($container->has('chubbyphp.deserializer.denormalizer'));
        self::assertTrue($container->has('chubbyphp.deserializer.denormalizer.objectmappingregistry'));

        /** @var Deserializer $deserializer */
        $deserializer = $container->get('chubbyphp.deserializer');

        /** @var Decoder $decoder */
        $decoder = $container->get('chubbyphp.deserializer.decoder');

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container->get('chubbyphp.deserializer.denormalizer');

        /** @var DenormalizerObjectMappingRegistry $objectMappingRegistry */
        $objectMappingRegistry = $container->get('chubbyphp.deserializer.denormalizer.objectmappingregistry');

        self::assertInstanceOf(Deserializer::class, $deserializer);
        self::assertInstanceOf(Decoder::class, $decoder);
        self::assertInstanceOf(Denormalizer::class, $denormalizer);
        self::assertInstanceOf(DenormalizerObjectMappingRegistry::class, $objectMappingRegistry);

        self::assertSame(['key' => 'value'], $decoder->decode('{"key":"value"}', 'application/json'));

        self::assertInstanceOf(\stdClass::class, $denormalizer->denormalize(\stdClass::class, ['key' => 'value']));
    }

    private function getStdClassMapping()
    {
        return new class() implements DenormalizationObjectMappingInterface {
            public function getClass(): string
            {
                return \stdClass::class;
            }

            public function getDenormalizationFactory(string $path, string $type = null): callable
            {
                return fn () => new \stdClass();
            }

            /**
             * @return array<int, DenormalizationFieldMappingInterface>
             */
            public function getDenormalizationFieldMappings(string $path, string $type = null): array
            {
                return [];
            }
        };
    }
}
