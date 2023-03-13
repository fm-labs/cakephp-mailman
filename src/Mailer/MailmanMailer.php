<?php
declare(strict_types=1);

namespace Mailman\Mailer;

use Cake\Mailer\Mailer;

/**
 * Class MailmanMailer
 *
 * @package Mailman\Mailer
 */
class MailmanMailer extends Mailer
{
    /**
     * Sends email.
     *
     * @param string|null $action The name of the mailer action to trigger.
     * @param array $args Arguments to pass to the triggered mailer action.
     * @param array $headers Headers to set.
     * @return array
     */
    public function send(?string $action = null, array $args = [], array $headers = []): array
    {
        $result = parent::send($action, $args, $headers);
        //Log::info(json_encode($result), ['email']);
        return $result;
    }
}
