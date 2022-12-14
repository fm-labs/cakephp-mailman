<?php
declare(strict_types=1);

namespace Mailman\Mailer\Transport;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Message;
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
        parent::__construct($config);

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
            Log::critical('MailTransport class not found: ' . $config['originalClassName']);
        } elseif (!isset($config['originalClassName']) && Plugin::isLoaded('DebugKit')) { // workaround for DebugKit
            $this->originalTransport = new DebugTransport();
        }
    }

    /**
     * @return \Cake\Mailer\AbstractTransport|null
     */
    public function getOriginalTransport(): ?AbstractTransport
    {
        return $this->originalTransport;
    }

    /**
     * Send mail
     *
     * @param \Cake\Mailer\Message $message Email instance.
     * @return array
     * @throws \Exception
     */
    public function send(Message $message): array
    {
        // dispacth `Email.beforeSend` event
        $event = EventManager::instance()->dispatch(new Event('Email.beforeSend', $message));
        if ($event->getResult() instanceof Message) {
            $message = $event->getResult();
        }

        $result = [];
        //$exception = null;
        try {
            if (!$this->originalTransport) {
                throw new \RuntimeException('Misconfigured mailman transport');
            }

            $result = $this->originalTransport->send($message);
        } catch (\Exception $ex) {
            //$exception = $ex;
            $error = $ex->getMessage();
            //if (Configure::read('debug')) {
            //    $error = sprintf("%s:%s %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString());
            //}
            $result = ['error' => $error];
        } finally {
            // dispatch `Email.afterSend` event
            EventManager::instance()->dispatch(new Event('Email.afterSend', $message, $result));

            // re-throw exception, if any
            //if ($exception !== null) {
            //    throw $exception;
            //}
        }

        return $result;
    }
}
