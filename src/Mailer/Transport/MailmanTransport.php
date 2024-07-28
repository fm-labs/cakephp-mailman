<?php
declare(strict_types=1);

namespace Mailman\Mailer\Transport;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Message;
use Mailman\Event\EmailEvent;

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
    public function __construct(array $config = [], ?AbstractTransport $originalTransport = null)
    {
        parent::__construct($config);

        if ($originalTransport !== null) {
            $this->originalTransport = $originalTransport;
            return;
        }

        $className = false;
        if (!empty($config['initialClassName'])) {
            $className = App::className(
                $config['initialClassName'],
                'Mailer/Transport',
                'Transport'
            );
        }

        if ($className) {
            unset($config['initialClassName']);
            $this->originalTransport = new $className($config);
        } elseif (!empty($config['initialClassName'])) {
            Log::critical('MailTransport class not found: ' . $config['initialClassName']);
        }
        //elseif (!isset($config['initialClassName']) && Plugin::isLoaded('DebugKit')) { // workaround for DebugKit
        //    $this->originalTransport = new DebugTransport();
        //}
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
        Log::debug("MailmanTransport::send (before)", ['email']);
        // dispatch `Email.beforeSend` event
        $event = EventManager::instance()->dispatch(new EmailEvent('Email.beforeSend', $message, [
            'transportClassName' => get_class($this->originalTransport)
        ]));
        if ($event->getResult() instanceof Message) {
            $message = $event->getResult();
        }

        $result = [];
        try {
            if (!$this->originalTransport) {
                throw new \RuntimeException('Misconfigured mailman transport');
            }

            $result = $this->originalTransport->send($message);
        } catch (\Exception $ex) {
            // write to default error log
            Log::error("MailmanTransport::send FAILED: " . get_class($ex) . ": " . $ex->getMessage());

            $error = $ex->getMessage();
            if (Configure::read('debug')) {
                $error = sprintf("%s:%s %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString());
            }
            $result = ['error' => $error];

            // dispatch `Email.error` event
            EventManager::instance()->dispatch(new EmailEvent('Email.error', $message, [
                'error' => $error,
                'exception' => $ex,
                'transportClassName' => get_class($this->originalTransport)
            ]));

            // re-throw the exception
            // throw $ex;
        } finally {
            //Log::debug("MailmanTransport::send (after)", ['email']);

            // dispatch `Email.afterSend` event
            EventManager::instance()->dispatch(new EmailEvent('Email.afterSend', $message, [
                'result' => $result,
                'transportClassName' => get_class($this->originalTransport)
            ]));
        }

        return $result;
    }
}
