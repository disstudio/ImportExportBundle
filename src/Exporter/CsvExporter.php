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

namespace Sylius\GridImportExport\Exporter;

use League\Csv\Writer;
use Sylius\GridImportExport\Exception\ExportFailedException;

final class CsvExporter extends AbstractExporter
{
    public const FORMAT = 'csv';

    public function __construct(
        string $exportDirectory,
        private readonly string $delimiter = ',',
    ) {
        parent::__construct($exportDirectory);
    }

    protected function getFormat(): string
    {
        return self::FORMAT;
    }

    public function export(array $data): string
    {
        $filename = $this->generateFilePath(self::FORMAT);

        try {
            $writer = Writer::createFromPath($filename, 'w+');
            $writer->setDelimiter($this->delimiter);
            $writer->insertAll($data);
        } catch (\Throwable $exception) {
            throw new ExportFailedException($exception->getMessage());
        }

        return $filename;
    }
}
