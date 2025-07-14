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

namespace Sylius\ImportExport\Denormalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final readonly class DefaultResourceDenormalizer implements ResourceDenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private RelationResolverInterface $relationResolver,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function denormalize(array $data, string $resourceClass): object
    {
        $metadata = $this->entityManager->getClassMetadata($resourceClass);
        $processedData = [];

        foreach ($data as $field => $value) {
            if (null === $value || '' === $value) {
                continue;
            }

            if ($metadata->hasAssociation($field)) {
                $associationMapping = $metadata->getAssociationMapping($field);
                $targetEntity = $associationMapping['targetEntity'];
                Assert::string($targetEntity);

                if ($metadata->isCollectionValuedAssociation($field)) {
                    if (is_array($value) && !empty($value)) {
                        $processedData[$field] = $this->relationResolver->resolveCollection($targetEntity, $value);
                    } else {
                        $processedData[$field] = [];
                    }
                } else {
                    if (is_array($value) && !empty($value)) {
                        $processedData[$field] = $this->relationResolver->resolveEntity($targetEntity, $value);
                    }
                }
            } elseif (is_array($value)) {
                $processedData[$field] = $this->processNestedArray($value);
            } else {
                $processedData[$field] = $value;
            }
        }

        $result = $this->denormalizer->denormalize($processedData, $resourceClass);
        Assert::object($result);

        return $result;
    }

    private function processNestedArray(array $data): array
    {
        return $data;
    }

    public function supports(string $resourceClass): bool
    {
        return true;
    }
}
