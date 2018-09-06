<?php

namespace Mailman;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Backend\Event\RouteBuilderEvent;
use Backend\View\BackendView;
use Banana\Application;
use Banana\Menu\Menu;
use Banana\Plugin\PluginInterface;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
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
class MailmanPlugin implements PluginInterface, BackendPluginInterface, EventListenerInterface
{
    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Sidebar.build' => ['callable' => 'buildBackendMenu', 'priority' => 80]
        ];
    }

    /**
     * @param Event $event
     */
    public function buildBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Mailman',
            'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
            'data-icon' => 'envelope-o',
            'children' => [
                'history' => [
                    'title' => __('Email History'),
                    'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
                    'data-icon' => 'history'
                ],
                'compose' => [
                    'title' => __('Compose Email'),
                    'url' => ['plugin' => 'Mailman', 'controller' => 'EmailComposer', 'action' => 'compose'],
                    'data-icon' => 'envelope-open'
                ],
            ]
        ]);
    }


    public function bootstrap(Application $app)
    {
        // inject MailmanTransport into all email transport configs
        $reflection = new ReflectionClass(Email::class);
        $property = $reflection->getProperty('_transportConfig');
        $property->setAccessible(true);
        $configs = $property->getValue();

        $configs = array_map(function($transport) {

            if (is_object($transport) && $transport instanceof MailmanTransport) {
                return $transport;
            }

            if (is_object($transport)) {
                $transport = new MailmanTransport([], $transport);
                return $transport;
            }

            $className = App::className($transport['className'], 'Mailer/Transport', 'Transport');
            if (!$className) {
                debug("Mailer Transport Class not found: " . $transport['className']);
                return $transport;
            }

            $transport['originalClassName'] = $transport['className'];
            $transport['className'] = 'Mailman.Mailman';

            return $transport;
        }, $configs);

        $property->setValue($configs);

        // attach listeners
        EventManager::instance()->on(new EmailListener());
    }

    public function routes(RouteBuilder $routes)
    {

    }

    public function middleware(MiddlewareQueue $middleware)
    {

    }

    public function backendBootstrap(Backend $backend)
    {
        EventManager::instance()->on($this);
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        // Admin routes
        $routes->fallbacks('DashedRoute');
    }
}
