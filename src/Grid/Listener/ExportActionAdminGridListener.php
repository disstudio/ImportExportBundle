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

namespace Sylius\GridImportExport\Grid\Listener;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

final class ExportActionAdminGridListener
{
    public function addExportMainAction(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();
        if (!$grid->hasActionGroup('main')) {
            $grid->addActionGroup(ActionGroup::named('main'));
        }

        $actionGroup = $grid->getActionGroup('main');
        if ($actionGroup->hasAction('export')) {
            return;
        }

        $action = Action::fromNameAndType('export', 'export');

        $actionGroup->addAction($action);
    }
}
