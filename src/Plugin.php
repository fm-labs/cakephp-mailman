<?php
declare(strict_types=1);

namespace Mailman;

use Cupcake\Plugin\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;
use Mailman\Event\EmailListener;

/**
 * Class MailmanPlugin
 *
 * @package Mailman
 */
class Plugin extends BasePlugin implements EventListenerInterface
{
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
     */
    public function buildAdminMenu(Event $event, \Cupcake\Menu\Menu $menu)
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

    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        // inject MailmanTransport into all email transport configs
        // @todo MailmanTransport injection not working in 3.8
        /*
        $reflection = new ReflectionClass(Email::class);
        $property = $reflection->getProperty('_transportConfig');
        $property->setAccessible(true);
        $configs = $property->getValue();

        $configs = array_map(function ($transport) {

            if (is_object($transport) && $transport instanceof MailmanTransport) {
                return $transport;
            }

            if (is_object($transport)) {
                $transport = new MailmanTransport([], $transport);

                return $transport;
            }

            $className = App::className($transport['className'], 'Mailer/Transport', 'Transport');
            if (!$className) {
                Log::critical("Mailer Transport Class not found: " . $transport['className']);

                return $transport;
            }

            $transport['originalClassName'] = $transport['className'];
            $transport['className'] = 'Mailman.Mailman';

            return $transport;
        }, $configs);

        $property->setValue($configs);
        */

        // attach listeners

        EventManager::instance()->on($this);
        EventManager::instance()->on(new EmailListener());
    }

    public function adminRoutes(RouteBuilder $routes)
    {
        // Admin routes
        $routes->fallbacks('DashedRoute');
    }
}
