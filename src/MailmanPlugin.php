<?php

namespace Mailman;

use Backend\Event\RouteBuilderEvent;
use Backend\View\BackendView;
use Banana\Menu\Menu;
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
            'Backend.SysMenu.build' => 'buildBackendSystemMenu',
            'Backend.Routes.build' => 'buildBackendRoutes',
            'View.beforeLayout' => ['callable' => 'beforeLayout']
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

    public function beforeLayout(Event $event)
    {
        if ($event->subject() instanceof BackendView && $event->subject()->plugin == "Mailman") {
            $menu = new Menu($this->_getMenuItems());
            $event->subject()->set('backend.sidebar.menu', $menu);
        }
    }

    protected function _getMenuItems()
    {
        return [
            'compose' => [
                'title' => __('Compose Email'),
                'url' => ['plugin' => 'Mailman', 'controller' => 'EmailComposer', 'action' => 'compose'],
                'data-icon' => 'envelope-open'
            ],
            'history' => [
                'title' => __('Email History'),
                'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
                'data-icon' => 'history'
            ]
        ];
    }

    /**
     * @param Event $event
     */
    public function buildBackendSystemMenu(Event $event)
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
}
