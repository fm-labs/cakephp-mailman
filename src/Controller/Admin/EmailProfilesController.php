<?php
declare(strict_types=1);

namespace Mailman\Controller\Admin;

use Cake\Http\Exception\NotImplementedException;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

/**
 * Class EmailProfilesController
 *
 * @package Mailman\Controller\Admin
 */
class EmailProfilesController extends AppController
{
    /**
     * Index action method
     *
     * @return void
     */
    public function index(): void
    {
        $transportKeys = TransportFactory::configured();
        $transports = array_map(function($key) {
            $config = TransportFactory::getConfig($key);
            $config['key'] = $key;
            return $config;
        }, $transportKeys);
        $transports = array_combine($transportKeys, $transports);

        $profileKeys = Mailer::configured();
        $profiles = array_map(function($key) {
            $config = Mailer::getConfig($key);
            $config['key'] = $key;
            return $config;
        }, $profileKeys);

        $profiles = array_combine($profileKeys, $profiles);
        $this->set(compact('profiles', 'transports'));
    }
}
