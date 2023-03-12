<?php

namespace Mailman\Event;

/**
 * EmailEvent class
 */
class EmailEvent extends \Cake\Event\Event
{
    public function getSubject(): \Cake\Mailer\Message
    {
        $subject = parent::getSubject();
        if ($subject instanceof \Cake\Mailer\Email) {
            $subject = $subject->getMessage();
        }
        return $subject;
    }
}