<?php

namespace Mailman;

use Backend\Event\RouteBuilderEvent;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Mailer\Email;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Mailman\Event\EmailListener;
use Mailman\Mailer\Transport\MailmanTransport;
use ReflectionClass;

/**
 * Class MailmanPlugin
 *
 * @package Mailman
 */
class MailmanPlugin implements EventListenerInterface
{
    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Sidebar.get' => 'getBackendSidebarMenu',
            'Backend.Routes.build' => 'buildBackendRoutes'
        ];
    }

    /**
     * Backend routes
     */
    public function buildBackendRoutes(RouteBuilderEvent $event)
    {
        // Admin routes
        $event->subject()->scope('/mailman',
            ['plugin' => 'Mailman', 'prefix' => 'admin', '_namePrefix' => 'mailman:admin:'],
            function (RouteBuilder $routes) {
                //$routes->connect('/:controller');
                $routes->fallbacks('DashedRoute');
            });
    }

    /**
     * @param Event $event
     */
    public function getBackendSidebarMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Mailman',
            'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
            'data-icon' => 'envelope-o',
        ]);
    }

    /**
     * Run Mailman plugin
     */
    public function __invoke()
    {
        // inject MailmanTransport into all email transport configs
        $reflection = new ReflectionClass(Email::class);
        $property = $reflection->getProperty('_transportConfig');
        $property->setAccessible(true);
        $configs = $property->getValue();

        foreach ((array) $configs as $name => &$transport) {
            if (is_object($transport) && $transport instanceof MailmanTransport) {
                continue;
            }
            if (is_object($transport)) {
                $configs[$name] = new MailmanTransport([], $transport);
                continue;
            }

            $className = App::className($transport['className'], 'Mailer/Transport', 'Transport');
            if (!$className) {
                continue;
            }

            $transport['originalClassName'] = $transport['className'];
            $transport['className'] = 'Mailman.Mailman';
        }
        $property->setValue($configs);

        // attach listeners
        EventManager::instance()->on(new EmailListener());
    }
}
