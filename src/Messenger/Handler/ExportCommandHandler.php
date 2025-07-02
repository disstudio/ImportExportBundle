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

use Sylius\GridImportExport\Entity\ExportProcessInterface;
use Sylius\GridImportExport\Messenger\Command\ExportCommand;
use Sylius\GridImportExport\Provider\Registry\ResourceDataProviderRegistryInterface;
use Sylius\GridImportExport\Resolver\ExporterResolverInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Metadata\RegistryInterface;

class ExportCommandHandler
{
    /** @param RepositoryInterface<ExportProcessInterface> $processRepository */
    public function __construct(
        public RegistryInterface $metadataRegistry,
        public RepositoryInterface $processRepository,
        public ResourceDataProviderRegistryInterface $dataProviderRegistry,
        public ExporterResolverInterface $exporterResolver,
    ) {
    }

    public function __invoke(ExportCommand $command): void
    {
        $process = $this->processRepository->find($command->processId);
        if (null === $process) {
            return;
        }

        $resourceMetadata = $this->metadataRegistry->get($process->getResource());

        $data = $this->dataProviderRegistry
            ->getProvider($resourceMetadata)
            ->getData($resourceMetadata, $process->getGrid(), $command->resourceIds, $process->getParameters())
        ;

        try {
            $resolver = $this->exporterResolver->resolve($process->getFormat());
            $outputPath = $resolver->export($data);

            $process->setStatus('success');
            $process->setOutput($outputPath);
        } catch (\Throwable $e) {
            $process->setStatus('failed');
            $process->setErrorMessage($e->getMessage());
        }
    }
}
