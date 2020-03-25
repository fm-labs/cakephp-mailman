<?php
declare(strict_types=1);

namespace Mailman\Mailer\Transport;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;
use Cake\Mailer\Transport\DebugTransport;

/**
 * Class MailmanTransport
 *
 * @package Mailman\src\Mailer\Transport
 */
class MailmanTransport extends AbstractTransport
{
    /**
     * @var \Cake\Mailer\AbstractTransport
     */
    protected $originalTransport = null;

    /**
     * Constructor
     *
     * @param array $config Configuration options.
     * @param \Cake\Mailer\AbstractTransport|null $originalTransport The transport that is to be decorated
     */
    public function __construct($config = [], ?AbstractTransport $originalTransport = null)
    {
        if ($originalTransport !== null) {
            $this->originalTransport = $originalTransport;

            return;
        }

        $className = false;
        if (!empty($config['originalClassName'])) {
            $className = App::className(
                $config['originalClassName'],
                'Mailer/Transport',
                'Transport'
            );
        }

        if ($className) {
            unset($config['originalClassName']);
            $this->originalTransport = new $className($config);
        } elseif (!empty($config['originalClassName'])) {
            Log::critical("MailTransport class not found: " . $config['originalClassName']);
        } elseif (!isset($config['originalClassName']) && Plugin::isLoaded('DebugKit')) { // workaround for DebugKit
            $this->originalTransport = new DebugTransport();
        }
    }

    public function getOriginalTransport()
    {
        return $this->originalTransport;
    }

    /**
     * Send mail
     *
     * @param \Cake\Mailer\Email $email Email instance.
     * @return array
     * @throws \Exception
     */
    public function send(Email $email)
    {
        // dispacth `Email.beforeSend` event
        $event = EventManager::instance()->dispatch(new Event('Email.beforeSend', $email));
        if ($event->result instanceof Email) {
            $email = $event->result;
        }

        $result = [];
        //$exception = null;
        try {
            if (!$this->originalTransport) {
                throw new \RuntimeException('Misconfigured mailman transport');
            }

            $result = $this->originalTransport->send($email);
        } catch (\Exception $ex) {
            //$exception = $ex;
            $error = $ex->getMessage();
            //if (Configure::read('debug')) {
            //    $error = sprintf("%s:%s %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString());
            //}
            $result = ['error' => $error];
        } finally {
            // dispatch `Email.afterSend` event
            EventManager::instance()->dispatch(new Event('Email.afterSend', $email, $result));

            // re-throw exception, if any
            //if ($exception !== null) {
            //    throw $exception;
            //}
        }

        return $result;
    }
}
