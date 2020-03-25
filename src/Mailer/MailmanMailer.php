<?php
declare(strict_types=1);

namespace Mailman\Mailer;

//use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\Exception\MissingActionException;
use Cake\Mailer\Mailer;

//use Mailman\Mailer\Storage\DatabaseEmailStorage;

/**
 * Class MailmanMailer
 *
 * @package Mailman\Mailer
 * @deprecated Use CakePHP's built-in Mailer class instead (since CakePHP 3.1)
 */
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
            if (!$this->_email->viewBuilder()->getTemplate()) {
                $this->_email->viewBuilder()->setTemplate($action);
            }

            call_user_func_array([$this, $action], $args);

            return $this->_send($this->_email);
        } finally {
            $this->reset();
        }
    }

    /**
     * @param \Cake\Mailer\Email $email
     * @return array
     * @deprecated
     */
    public function sendEmail(Email $email, $content = null, $throwExceptions = false)
    {
        return $this->_send($email, $content, $throwExceptions);
    }

    /**
     * Send email with Mailman hooks
     *
     * @param \Cake\Mailer\Email $email
     * @param bool $content
     * @return array
     * @throws \Exception
     */
    protected function _send(Email $email, $content = null, $throwExceptions = false)
    {

        $result = null;
        $exception = null;
        try {
            $result = $email->send($content);
        } catch (\Exception $ex) {
            $result = ['error' => $ex->getMessage()];
            $exception = $ex;
        }

        //@TODO Remove unused code (Already reimplemented in EmailListener)
        //try {
        //    $dbStorage = new DatabaseEmailStorage();
        //    $dbStorage->store($email, $result);
        //} catch (\Exception $ex) {
        //    Log::error('Failed to store email message: ' . $ex->getMessage());
        //}

        if ($exception !== null && $throwExceptions) {
            throw $exception;
        }

        return $result;
    }
}
