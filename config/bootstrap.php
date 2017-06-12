<?php

use Cake\Log\Log;

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
