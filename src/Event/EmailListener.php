<?php
declare(strict_types=1);

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
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function beforeSend(Event $event)
    {
        $email = $event->getSubject();
        if (!($email instanceof Email)) {
            Log::warning("[mailman] Event subject IS NOT an email object");

            return;
        }

        Log::info(sprintf(
            '[mailman][email][outbox] %s -> %s: %s',
            join(',', $email->getFrom()),
            join(',', $email->getTo()),
            $email->getOriginalSubject()
        ), ['email']);
    }

    /**
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function afterSend(Event $event)
    {
        try {
            $email = $event->getSubject();
            if (!($email instanceof Email)) {
                Log::warning("[mailman] Event subject IS NOT an email object");

                return;
            }

            $result = $event->getData();
            $this->_emails[] = $result;

            $dbStorage = new DatabaseEmailStorage();
            $dbStorage->store($email, $result);

            if (isset($result['error'])) {
                Log::error(sprintf(
                    '[mailman][email][error] %s -> %s: %s: %s',
                    join(',', $email->getFrom()),
                    join(',', $email->getTo()),
                    $email->getOriginalSubject(),
                    $result['error']
                ), ['email']);
            } else {
                Log::info(sprintf(
                    '[mailman][email][sent] %s -> %s: %s',
                    join(',', $email->getFrom()),
                    join(',', $email->getTo()),
                    $email->getOriginalSubject()
                ), ['email']);
            }
        } catch (\Exception $ex) {
            Log::error('[mailman][storage][db] Failed to store email message: ' . $ex->getMessage(), ['email']);
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
                        $event->getSubject()->components()->get('Flash')->error('Email sending failed: ' . $email['error']);
                    } else {
                        $event->getSubject()->components()->get('Flash')->success('Email sent');
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
            'Email.beforeSend'          => 'beforeSend',
            'Email.afterSend'           => 'afterSend',
            'Controller.beforeRender'   => 'beforeRender',
        ];
    }
}
