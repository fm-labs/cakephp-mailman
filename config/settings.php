<?php
return [
    'Settings' => [
        'Mailman' => [
            'groups' => [
                'Mailman.Debug' => [
                    'label' => __d('mailman', 'Email Debug Settings'),
                ],
            ],

            'schema' => [
                'Mailman.Debug.flashEmails' => [
                    'group' => 'Mailman.Debug',
                    'type' => 'boolean',
                    'label' => __d('mailman', 'Flash Emails'),
                    'help' => __d('mailman', 'Show emails as flash messages when DEBUG is enabled.'),
                ],
            ],
        ],
    ],
];
