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

namespace Sylius\GridImportExport\Provider\ResourceData;

use Sylius\GridImportExport\Exception\ProviderException;

interface ResourceDataProviderInterface
{
    /**
     * @param class-string $resource
     * @param mixed[] $resourceIds
     *
     * @return array<array<string, mixed>>
     *
     * @throws ProviderException
     */
    public function getData(string $resource, array $resourceIds): array;
}
