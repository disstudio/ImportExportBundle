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

namespace Sylius\ImportExport\Provider\Parameters;

use Sylius\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;

interface GridExportParametersProviderInterface
{
    /** @return array<string, mixed> */
    public function getParameters(MetadataInterface $metadata, string $gridName, Request $request): array;
}
