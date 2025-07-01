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

namespace Sylius\GridImportExport\Grid\Checker;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Resource\Metadata\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ExportableChecker implements ExportableCheckerInterface
{
    /**
     * @param array<array-key, string> $allowedSections
     * @param array<array-key, string> $allowedResources
     */
    public function __construct(
        private RequestStack $requestStack,
        private RegistryInterface $resourceRegistry,
        private array $allowedSections,
        private array $allowedResources,
    ) {
    }

    public function canBeExported(Grid $grid): bool
    {
        $resourceClass = $grid->getDriverConfiguration()['class'] ?? null;
        if (null === $resourceClass) {
            return false;
        }

        $request = $this->requestStack->getMainRequest();
        if (!$request instanceof Request) {
            return false;
        }

        if (!$request->attributes->has('_sylius')) {
            return false;
        }

        $syliusAttributes = $request->attributes->all()['_sylius'];
        if (!in_array($syliusAttributes['section'] ?? null, $this->allowedSections)) {
            return false;
        }

        $resourceMetadata = $this->resourceRegistry->getByClass($resourceClass);

        return in_array($resourceMetadata->getAlias(), $this->allowedResources);
    }
}
