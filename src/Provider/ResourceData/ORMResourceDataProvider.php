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

namespace Sylius\ImportExport\Provider\ResourceData;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\ImportExport\Provider\ResourceIdentifierProviderInterface;
use Sylius\ImportExport\Serializer\DefaultSerializationGroups;
use Sylius\ImportExport\Serializer\ExportAwareItemNormalizer;
use Sylius\Resource\Metadata\MetadataInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ORMResourceDataProvider implements ResourceDataProviderInterface
{
    public function __construct(
        private ResourceIdentifierProviderInterface $identifierProvider,
        private EntityManagerInterface $entityManager,
        private NormalizerInterface $serializer,
    ) {
    }

    public function getData(MetadataInterface $resource, array $resourceIds, array $parameters): array
    {
        $identifier = $this->identifierProvider->getIdentifierField($resource);

        $repository = $this->entityManager->getRepository($resource->getClass('model'));

        $queryBuilder = $repository->createQueryBuilder('o');
        $rawData = $queryBuilder
            ->andWhere($queryBuilder->expr()->in('o.' . $identifier, $resourceIds))
            ->getQuery()
            ->getResult()
        ;

        /** @phpstan-ignore-next-line */
        return $this->serializer->normalize($rawData, context: [
            ExportAwareItemNormalizer::EXPORT_CONTEXT_KEY => true,
            'groups' => $parameters['serialization_groups'] ?? [DefaultSerializationGroups::EXPORT_GROUP],
        ]);
    }
}
