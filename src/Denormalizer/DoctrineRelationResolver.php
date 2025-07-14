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
use Webmozart\Assert\Assert;

final readonly class DoctrineRelationResolver implements RelationResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function resolveEntity(string $entityClass, array $data): ?object
    {
        if (empty($data)) {
            return null;
        }

        Assert::classExists($entityClass);
        $repository = $this->entityManager->getRepository($entityClass);
        $identifierField = $this->getIdentifierField($entityClass, $data);

        if (!isset($data[$identifierField])) {
            return null;
        }

        $identifier = $data[$identifierField];

        if ('id' === $identifierField) {
            return $repository->find($identifier);
        }

        return $repository->findOneBy([$identifierField => $identifier]);
    }

    public function resolveCollection(string $entityClass, array $dataCollection): array
    {
        if (empty($dataCollection)) {
            return [];
        }

        $entities = [];
        foreach ($dataCollection as $data) {
            $entity = $this->resolveEntity($entityClass, $data);
            if (null !== $entity) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    private function getIdentifierField(string $entityClass, array $data): string
    {
        if (isset($data['code'])) {
            return 'code';
        }

        if (isset($data['id'])) {
            return 'id';
        }

        return 'id';
    }
}
