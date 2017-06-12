<?php

namespace Mailman\Test\TestCase;

use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;

/**
 * Class MailmanTestCase
 *
 * @package Mailman\Test\TestCase
 */
class MailmanTestCase extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Email::configTransport('test', [
            'className' => 'Mailman.Mailman',
            'originalClassName' => 'Debug'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        Email::dropTransport('test');
    }
}