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

namespace Sylius\GridImportExport\Entity;

use Sylius\Resource\Model\TimestampableTrait;

class ExportProcess extends Process implements ExportProcessInterface
{
    use TimestampableTrait;

    protected string $format;

    protected array $resourceIds = [];

    public function getType(): string
    {
        return ExportProcessInterface::TYPE;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getResourceIds(): array
    {
        return $this->resourceIds;
    }

    public function setResourceIds(array $resourceIds): void
    {
        $this->resourceIds = $resourceIds;
    }
}
