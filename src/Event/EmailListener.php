<?php

namespace Mailman\Event;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Mailman\Mailer\Storage\DatabaseEmailStorage;

/**
 * Class EmailListener
 *
 * @package Mailman\Event
 */
class EmailListener implements EventListenerInterface
{
    /**
     * @var array List of captured email results
     */
    protected $_emails = [];

    /**
     * @param Event $event
     * @return void
     */
    public function beforeSend(Event $event)
    {
        $email = $event->subject();
        if (!($email instanceof Email)) {
            Log::warning("[mailman] Event subject IS NOT an email object");

            return;
        }

        Log::info(sprintf(
            '[mailman][email][outbox] %s -> %s: %s',
            join(',', $email->from()),
            join(',', $email->to()),
            $email->getOriginalSubject()
        ), ['email']);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function afterSend(Event $event)
    {
        try {
            $email = $event->subject();
            if (!($email instanceof Email)) {
                Log::warning("[mailman] Event subject IS NOT an email object");

                return;
            }

            $result = $event->data();
            $this->_emails[] = $result;

            $dbStorage = new DatabaseEmailStorage();
            $dbStorage->store($email, $result);

            if (isset($result['error'])) {
                Log::error(sprintf(
                    '[mailman][email][error] %s -> %s: %s: %s',
                    join(',', $email->from()),
                    join(',', $email->to()),
                    $email->getOriginalSubject(),
                    $result['error']
                ), ['email']);
            } else {
                Log::info(sprintf(
                    '[mailman][email][sent] %s -> %s: %s',
                    join(',', $email->from()),
                    join(',', $email->to()),
                    $email->getOriginalSubject()
                ), ['email']);
            }
        } catch (\Exception $ex) {
            Log::error('[mailman][storage][db] Failed to store email message: ' . $ex->getMessage(), ['email']);
        }
    }

    /**
     * @param Event $event
     * @return void
     * @deprecated Use DebugKit plugin instead to debug email messages
     */
    public function beforeRender(Event $event)
    {
        // show emails as flash messages in debug mode
        if (Configure::read('debug') == true && Configure::read('Mailman.Debug.flashEmails') == true) {
            if ($event->subject()->components()->has('Flash')) {
                foreach ($this->_emails as $email) {
                    if (isset($email['error'])) {
                        $event->subject()->components()->get('Flash')->error('Email sending failed: ' . $email['error']);
                    } else {
                        $event->subject()->components()->get('Flash')->success('Email sent');
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Email.beforeSend'          => 'beforeSend',
            'Email.afterSend'           => 'afterSend',
            'Controller.beforeRender'   => 'beforeRender'
        ];
    }
}
