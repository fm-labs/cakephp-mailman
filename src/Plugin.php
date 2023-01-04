<?php
declare(strict_types=1);

namespace Mailman;

use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Core\BasePlugin;
use Mailman\Event\EmailListener;

/**
 * Class MailmanPlugin
 *
 * @package Mailman
 */
class Plugin extends BasePlugin
{

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
        EventManager::instance()->on(new EmailListener());

        /**
         * Administration plugin
         */
        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \Mailman\Admin());
        }
    }
}
