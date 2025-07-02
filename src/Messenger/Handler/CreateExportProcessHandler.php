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

namespace Sylius\GridImportExport\Messenger\Handler;

use Sylius\GridImportExport\Entity\ProcessInterface;
use Sylius\GridImportExport\Factory\ProcessFactoryInterface;
use Sylius\GridImportExport\Messenger\Command\CreateExportProcess;
use Sylius\GridImportExport\Messenger\Command\ExportCommand;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateExportProcessHandler
{
    /**
     * @param RepositoryInterface<ProcessInterface> $processRepository
     * @param int<1, max> $batchSize
     */
    public function __construct(
        public ProcessFactoryInterface $processFactory,
        public RepositoryInterface $processRepository,
        public MessageBusInterface $messageBus,
        public int $batchSize = 100,
    ) {
    }

    public function __invoke(CreateExportProcess $command): void
    {
        $process = $this->processFactory->createExportProcess($command);

        $this->processRepository->add($process);

        foreach (array_chunk($process->getResourceIds(), $this->batchSize) as $batch) {
            $this->messageBus->dispatch(new ExportCommand(
                processId: $process->getUuid(),
                resourceIds: $batch,
            ));
        }
    }
}
