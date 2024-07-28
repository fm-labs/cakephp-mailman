<?php
declare(strict_types=1);

namespace Mailman\Mailer;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Mailman\Event\EmailEvent;

/**
 * Class EmailListener
 *
 * @package Mailman\Event
 */
class EmailLogger implements EventListenerInterface
{
    /**
     * @var array List of captured email results
     */
    protected $_emails = [];

    /**
     * @param \Mailman\Event\EmailEvent $event
     * @return void
     */
    public function beforeSend(EmailEvent $event)
    {
        $email = $event->getSubject();
        Log::info(sprintf(
            '[mailman][email][outbox] %s -> %s: %s',
            join(',', $email->getFrom()),
            join(',', $email->getTo()),
            $email->getOriginalSubject()
        ),  ['scope' => ['email', 'mailman']]);
    }

    /**
     * @param \Mailman\Event\EmailEvent $event
     * @return void
     */
    public function afterSend(EmailEvent $event)
    {
        try {
            $email = $event->getSubject();
            $data = $event->getData();
            $result = $data['result'] ?? [];
            $this->_emails[] = $result;

            if (isset($result['error'])) {
                Log::error(sprintf(
                    '[mailman][email][error] %s -> %s: %s: %s',
                    join(',', $email->getFrom()),
                    join(',', $email->getTo()),
                    $email->getOriginalSubject(),
                    $result['error']
                ), ['scope' => ['email', 'mailman']]);
            } else {
                Log::info(sprintf(
                    '[mailman][email][sent] %s -> %s: %s',
                    join(',', $email->getFrom()),
                    join(',', $email->getTo()),
                    $email->getOriginalSubject(),
                ), ['scope' => ['email', 'mailman']]);
            }
        } catch (\Exception $ex) {
            Log::error('[mailman][logger] Failed to log message: ' . $ex->getMessage());
        }
    }

    /**
     * @param \Cake\Event\Event $event
     * @return void
     * @deprecated Use DebugKit plugin instead to debug email messages
     */
    public function beforeRender(Event $event)
    {
        // show emails as flash messages in debug mode
        if (Configure::read('debug') == true && Configure::read('Mailman.Debug.flashEmails') == true) {
            if ($event->getSubject()->components()->has('Flash')) {
                foreach ($this->_emails as $email) {
                    if (isset($email['error'])) {
                        $event->getSubject()->components()->get('Flash')
                            ->error('Email sending failed: ' . $email['error']);
                    } else {
                        $event->getSubject()->components()->get('Flash')
                            ->success('Email sent');
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Email.beforeSend' => 'beforeSend',
            'Email.afterSend' => 'afterSend',
            'Controller.beforeRender' => 'beforeRender',
        ];
    }
}
