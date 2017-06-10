<?php

namespace Mailman;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;

class MailmanPlugin implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Backend.Menu.get' => 'getBackendMenu',
            'Backend.Routes.build' => 'buildBackendRoutes'
        ];
    }

    public function buildBackendRoutes()
    {
        // Admin routes
        Router::scope('/mailman/admin', ['plugin' => 'Mailman', 'prefix' => 'admin', '_namePrefix' => 'mailman:admin:'], function ($routes) {
            $routes->connect('/:controller');
            $routes->fallbacks('DashedRoute');
        });
    }

    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Mailman',
            'url' => ['plugin' => 'Mailman', 'controller' => 'EmailMessages', 'action' => 'index'],
            'data-icon' => 'envelope-o',
        ]);
    }

    public function __invoke()
    {
        \Cake\Event\EventManager::instance()->on(new \Mailman\Event\EmailListener());
    }
}
