<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ImportExport\Tests\Unit\Serializer;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Serializer\AbstractItemNormalizer;
use PHPUnit\Framework\TestCase;
use Sylius\ImportExport\Serializer\ExportAwareItemNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class ExportAwareItemNormalizerTest extends TestCase
{
    public function testSetSerializerPropagatesToDecoratedService(): void
    {
        $decorated = $this->createMock(AbstractItemNormalizer::class);
        $serializer = $this->createMock(SerializerInterface::class);

        $decorated->expects($this->once())
            ->method('setSerializer')
            ->with($serializer);

        $normalizer = new ExportAwareItemNormalizer(
            propertyNameCollectionFactory: $this->createMock(PropertyNameCollectionFactoryInterface::class),
            propertyMetadataFactory: $this->createMock(PropertyMetadataFactoryInterface::class),
            iriConverter: $this->createMock(IriConverterInterface::class),
            resourceClassResolver: $this->createMock(ResourceClassResolverInterface::class),
            decorated: $decorated,
        );

        $normalizer->setSerializer($serializer);
    }
}
