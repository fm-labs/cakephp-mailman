<?php

use Backend\Lib\Backend;
use Cake\Core\Plugin;
use Cake\Log\Log;
use Cake\Mailer\Email;

// Mailman log config
if (!Log::config('mailman')) {
    Log::config('mailman', [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'mailman',
        //'levels' => ['notice', 'info', 'debug'],
        'scopes' => ['mailman', 'email']
    ]);
}

// Automatically use Mailman Email class instead of CakePHP's Mailer/Email class
//@TODO Make this optional

// Backend hook
if (Plugin::loaded('Backend')) {
    Backend::hookPlugin('Mailman');
}