<?php
declare(strict_types=1);

namespace Mailman;

use Cake\Core\App;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Log\Engine\FileLog;
use Cake\Log\Log;
use Cake\Mailer\TransportFactory;
use Mailman\Mailer\EmailLogger;
use Mailman\Mailer\Storage\DatabaseEmailStorage;
use Mailman\Mailer\Transport\MailmanTransport;
use ReflectionClass;

/**
 * Class MailmanPlugin
 *
 * @package Mailman
 */
class MailmanPlugin extends BasePlugin
{

    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        if (\Cake\Core\Plugin::isLoaded('Settings')) {
            Configure::load('Mailman', 'settings');
        }

        /**
         * Log configuration
         */
        if (!Log::getConfig('email')) {
            Log::setConfig('email', [
                'className' => FileLog::class,
                'path' => LOGS,
                'file' => 'email',
                //'levels' => ['notice', 'info', 'debug'],
                'scopes' => ['email', 'mailman'],
            ]);
        }

        $registry = TransportFactory::getRegistry();

//        $configured = TransportFactory::configured();
//        foreach ($configured as $name) {
//            $config = TransportFactory::getConfig($name);
//            debug($config);
//
//            if ($registry->has($name)) {
//                debug($registry->get($name));
//            }
//        }

        $reflection = new ReflectionClass(TransportFactory::class);
        $property = $reflection->getProperty('_config');
        $property->setAccessible(true);
        /** @var array<\Cake\Mailer\AbstractTransport|array> $configs */
        $configs = $property->getValue();

        foreach ($configs as $name => $transport) {
            if (is_object($transport)) {
                if (!$transport instanceof MailmanTransport) {
                    $configs[$name] = new MailmanTransport([], $transport);
                }
                continue;
            }

            $className = App::className($transport['className'], 'Mailer/Transport', 'Transport');
            if (!$className || $className === MailmanTransport::class) {
                continue;
            }

            $transport['initialClassName'] = $transport['className'];
            $transport['className'] = 'Mailman.Mailman';

            $configs[$name] = $transport;
            $registry->unload($name);
        }
        $property->setValue($configs);

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
        EventManager::instance()->on(new EmailLogger());
        EventManager::instance()->on(new DatabaseEmailStorage());

        /**
         * Administration plugin
         */
        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \Mailman\MailmanAdmin());
        }

        $debugkitPanels = Configure::read('DebugKit.panels', []);
        $debugkitPanels['DebugKit.Mail'] = false;
        Configure::write('DebugKit.panels', $debugkitPanels);
    }
}
