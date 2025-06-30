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

namespace Sylius\GridImportExport\Factory;

use Sylius\GridImportExport\Entity\ProcessInterface;
use Sylius\GridImportExport\Messenger\Command\ExportCommand;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Uid\Uuid;

final readonly class ProcessFactory implements ProcessFactoryInterface
{
    /** @param FactoryInterface<ProcessInterface> $factory */
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function createNew(): ProcessInterface
    {
        return $this->factory->createNew();
    }

    public function createFromExportCommand(ExportCommand $command): ProcessInterface
    {
        $process = $this->createNew();
        $process->setUuid(Uuid::v7()->toRfc4122());
        $process->setType(ProcessInterface::TYPE_EXPORT);
        $process->setResource($command->resource);
        $process->setFormat($command->format);
        $process->setResourceIds($command->resourceIds);
        $process->setStatus('processing');

        return $process;
    }
}
