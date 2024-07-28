<?php
return [
    'Settings' => [
        'Mailman' => [
            'groups' => [
                'Mailman.Transport' => [
                    'label' => __d('mailman', 'Mailman Transport Settings'),
                ],
                'Mailman.Debug' => [
                    'label' => __d('mailman', 'Mailman Debug Settings'),
                ],
            ],

            'schema' => [
                'Mailman.Transport.enable' => [
                    'group' => 'Mailman.Transport',
                    'type' => 'boolean',
                    'label' => __d('mailman', 'Enable Mailman Transport'),
                    'help' => __d('mailman', 'Enable Email sending via Mailman Transport.'),
                ],
                'Mailman.Transport.enableEmailLogger' => [
                    'group' => 'Mailman.Transport',
                    'type' => 'boolean',
                    'label' => __d('mailman', 'Enable Email Logger'),
                    'help' => __d('mailman', 'Log emails with application logger interface'),
                ],
                'Mailman.Transport.enableDatabaseStorage' => [
                    'group' => 'Mailman.Transport',
                    'type' => 'boolean',
                    'label' => __d('mailman', 'Enable Database Storage'),
                    'help' => __d('mailman', 'Log emails to database'),
                ],
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
