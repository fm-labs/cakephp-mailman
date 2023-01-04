<?php
declare(strict_types=1);

namespace Mailman;

use Admin\Core\BaseAdminPlugin;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Routing\RouteBuilder;


/**
 * Class Admin
 *
 * @package Media
 */
class Admin extends BaseAdminPlugin implements EventListenerInterface
{
    /**
     * @inheritDoc
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->connect(
            '/',
            ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index']
        );

        $routes->fallbacks('DashedRoute');
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_primary' => ['callable' => 'buildAdminMenu', 'priority' => 80],
        ];
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cupcake\Menu\MenuItemCollection $menu
     * @return void
     */
    public function buildAdminMenu(EventInterface $event, \Cupcake\Menu\MenuItemCollection $menu): void
    {
        $menu->addItem([
            'title' => 'Mailman',
            'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
            'data-icon' => 'envelope-o',
            'children' => [
                'history' => [
                    'title' => __('Email History'),
                    'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
                    'data-icon' => 'history',
                ],
                'compose' => [
                    'title' => __('Compose Email'),
                    'url' => ['plugin' => 'Mailman', 'controller' => 'EmailComposer', 'action' => 'compose'],
                    'data-icon' => 'envelope-open',
                ],
            ],
        ]);
    }

}
