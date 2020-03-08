<?php

use Cake\Log\Log;
use Cake\Core\Configure;

// Mailman log config
if (!Log::getConfig('mailman')) {
    Log::setConfig('mailman', [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'mailman',
        //'levels' => ['notice', 'info', 'debug'],
        'scopes' => ['mailman', 'email'],
    ]);
}

if (\Cake\Core\Plugin::isLoaded('DebugKit')) {
    //if (!Configure::check('DebugKit.panels')) {
    //    Configure::write('DebugKit.panels.DebugKit.Mail', false);
    //}
}
