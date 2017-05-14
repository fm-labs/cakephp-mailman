<?php

namespace Mailman\Mailer;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Exception\MissingActionException;
use Cake\Mailer\Mailer;
use Cake\Mailer\Email;
use Mailman\Mailer\Storage\DatabaseEmailStorage;

class MailmanMailer extends Mailer
{
    /**
     * Sends email.
     *
     * @param string $action The name of the mailer action to trigger.
     * @param array $args Arguments to pass to the triggered mailer action.
     * @param array $headers Headers to set.
     * @return array
     * @throws \Cake\Mailer\Exception\MissingActionException
     * @throws \BadMethodCallException
     * @observe
     */
    public function send($action, $args = [], $headers = [])
    {

        try {
            if (!method_exists($this, $action)) {
                throw new MissingActionException([
                    'mailer' => $this->getName() . 'Mailer',
                    'action' => $action,
                ]);
            }

            $this->_email->setHeaders($headers);
            if (!$this->_email->viewBuilder()->template()) {
                $this->_email->viewBuilder()->template($action);
            }

            call_user_func_array([$this, $action], $args);

            $result = $this->_send($this->_email);
        } finally {
            $this->reset();
        }

        return $result;
    }

    /**
     * @param Email $email
     * @return array
     */
    public function sendEmail(Email $email, $content = null, $throwExceptions = false)
    {
        return $this->_send($email, $content, $throwExceptions);
    }

    /**
     * Send email with Mailman hooks
     *
     * @param Email $email
     * @param bool $throwExceptions
     * @return array
     * @throws \Exception
     */
    protected function _send(Email $email, $content = null, $throwExceptions = false)
    {


        $result = null;
        $exception = null;
        try {
            //@TODO dispatch event 'Email.beforeSend'
            $result = $email->send($content);
            //@TODO dispatch event 'Email.afterSend'

        } catch (\Exception $ex) {
            $result = ['error' => $ex->getMessage()];
            $exception = $ex;
        }

        try {
            $dbStorage = new DatabaseEmailStorage();
            $dbStorage->store($email, $result);

        } catch (\Exception $ex) {
            Log::error('Failed to store email message: ' . $ex->getMessage());
        }

        if ($exception !== null && $throwExceptions) {
            throw $exception;
        }

        return $result;
    }
}