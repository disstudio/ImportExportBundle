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

namespace Sylius\ImportExport\Controller;

use Sylius\ImportExport\Messenger\Command\CreateImportProcess;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ImportAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $commandBus,
        private string $importForm,
    ) {
    }

    public function __invoke(Request $request, string $grid): Response
    {
        /** @var Session $session */
        $session = $request->getSession();

        $form = $this->formFactory->create($this->importForm);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $session->getFlashBag()->add('error', 'sylius_import_export.import_form_invalid');

            return new RedirectResponse($request->headers->get('referer') ?? '/');
        }

        $data = $form->getData();
        $format = $data['format'];
        $filePath = $data['filePath'];
        $resourceClass = $data['resourceClass'];

        try {
            $this->commandBus->dispatch(new CreateImportProcess(
                resource: $resourceClass,
                format: $format,
                filePath: $filePath,
                parameters: [],
            ));

            $session->getFlashBag()->add('success', 'sylius_import_export.import_started');
        } catch (\Throwable) {
            $session->getFlashBag()->add('error', 'sylius_import_export.import_failed');
        }

        return new RedirectResponse($request->headers->get('referer') ?? '/');
    }
}
