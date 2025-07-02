<?php

declare(strict_types=1);

namespace Sylius\GridImportExport\Entity;

interface ExportProcessInterface extends ProcessInterface
{
    public const TYPE = 'export';

    public function getFormat(): string;

    public function setFormat(string $format): void;

    public function getResourceIds(): array;

    public function setResourceIds(array $resourceIds): void;

    public function getGrid(): string;

    public function setGrid(string $grid): void;

    public function getParameters(): array;

    public function setParameters(array $parameters): void;

}
