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

        Log::info(sprintf('[mailman][email][outbox] %s -> %s: %s',
            join(',', $email->from()),
            join(',', $email->to()),
            $email->subject()
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

            Log::info(sprintf('[mailman][email][sent] %s -> %s: %s',
                join(',', $email->from()),
                join(',', $email->to()),
                $email->subject()
            ), ['email']);

        } catch (\Exception $ex) {
            Log::error('[mailman][storage][db] Failed to store email message: ' . $ex->getMessage(), ['email']);
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function transportError(Event $event)
    {
        $this->afterSend($event);
    }

    /**
     * @param Event $event
     * @return void
     * @deprecated Use DebugKit plugin instead to debug email messages
     */
    public function beforeRender(Event $event)
    {
        // show emails as flash messages in debug mode
        if (Configure::read('debug')) {
            if ($event->subject()->components()->has('Flash')) {
                foreach ($this->_emails as $email) {
                    if (isset($email['error'])) {
                        $event->subject()->components()->get('Flash')->error('EmailListener: Email sending failed: ' . $email['error']);
                    } else {
                        $event->subject()->components()->get('Flash')->success('EmailListener: Email sent: ' . $email['message']);
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
            'Email.transportError'      => 'transportError',
            'Controller.beforeRender'   => 'beforeRender'
        ];
    }
}
