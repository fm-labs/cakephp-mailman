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
                'scopes' => ['email'],
            ]);
        }

        if (!Log::getConfig('mailman')) {
            Log::setConfig('mailman', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'file' => 'mailman',
                //'levels' => ['notice', 'info', 'debug'],
                'scopes' => ['mailman'],
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
        //debug($configs);
        $property->setValue($configs);

        // attach listeners
        EventManager::instance()->on(new EmailLogger());
        EventManager::instance()->on(new DatabaseEmailStorage());

        /**
         * Administration plugin
         */
        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \Mailman\MailmanAdmin());
        }

        //$debugkitPanels = Configure::read('DebugKit.panels', []);
        //$debugkitPanels['DebugKit.Mail'] = false;
        //Configure::write('DebugKit.panels', $debugkitPanels);
    }
}
