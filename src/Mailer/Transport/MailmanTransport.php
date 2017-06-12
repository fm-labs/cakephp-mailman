<?php

namespace Mailman\Mailer\Transport;

use Cake\Core\App;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;

/**
 * Class MailmanTransport
 *
 * @package Mailman\src\Mailer\Transport
 */
class MailmanTransport extends AbstractTransport
{

    /**
     * @var AbstractTransport
     */
    public $originalTransport = null;

    /**
     * Constructor
     *
     * @param array $config Configuration options.
     * @param AbstractTransport|null $originalTransport The transport that is to be decorated
     */
    public function __construct($config = [], AbstractTransport $originalTransport = null)
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
        }
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
        $exception = null;
        try {
            if (!$this->originalTransport) {
                throw new \RuntimeException('Misconfigured mailman transport');
            }

            $result = $this->originalTransport->send($email);

        } catch (\Exception $ex) {
            $exception = $ex;
            $result = ['error' => $ex->getMessage()];

            // dispacth `Email.transportError` event
            EventManager::instance()->dispatch(new Event('Email.transportError', $email, $result));

        } finally {
            // dispacth `Email.afterSend` event
            EventManager::instance()->dispatch(new Event('Email.afterSend', $email, $result));
        }

        // re-throw exception, if any
        //if ($exception !== null) {
        //    throw $exception;
        //}

        return $result;
    }
}