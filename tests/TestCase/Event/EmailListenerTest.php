<?php

namespace Mailman\Test\TestCase\Event;

use Cake\Event\Event;
use Cake\Mailer\Email;
use Mailman\Event\EmailListener;
use Mailman\Test\TestCase\MailmanTestCase;

/**
 * Class EmailListenerTest
 *
 * @package Mailman\Test\TestCase\Event
 */
class EmailListenerTest extends MailmanTestCase
{
    /**
     * @var EmailListener
     */
    public $emailListener;

    /**
     * @var Email
     */
    public $email;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();

        $this->email = new Email([
            'transport' => 'test',
            'from' => 'test@example.org',
            'to' => 'foo@example.org',
            'subject' => 'Test'
        ]);

        $this->emailListener = new EmailListener();
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->email = null;
        $this->emailListener = null;
    }

    /**
     * Test beforeSend method
     */
    public function testBeforeSend()
    {
        $event = new Event('Email.beforeSend', $this->email);
        $this->emailListener->beforeSend($event);
    }

    /**
     * Test beforeSend method
     */
    public function testAfterSend()
    {
        $event = new Event('Email.afterSend', $this->email);
        $this->emailListener->afterSend($event);
    }

    /**
     * Test beforeSend method
     */
    public function testTransportError()
    {
        $event = new Event('Email.transportError', $this->email);
        $this->emailListener->transportError($event);
    }
}
