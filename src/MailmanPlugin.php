<?php

namespace Mailman;

use Cake\Core\App;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Mailman\Event\EmailListener;
use Mailman\src\Mailer\Transport\MailmanTransport;
use ReflectionClass;

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
        // inject MailmanTransport into all email transport configs
        $reflection = new ReflectionClass(Email::class);
        $property = $reflection->getProperty('_transportConfig');
        $property->setAccessible(true);
        $configs = $property->getValue();

        foreach ($configs as $name => &$transport) {
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
