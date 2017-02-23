<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 2/15/16
 * Time: 2:00 PM
 */

namespace Mailman\Email;

use Cake\Mailer\Email as CakeEmail;

class Email extends CakeEmail
{
    public function __construct($config = null)
    {
        parent::__construct($config);
    }
}