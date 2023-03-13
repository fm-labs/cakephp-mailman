<?php
declare(strict_types=1);

namespace Mailman\Test\TestCase\Event;

use Cake\Mailer\Message;
use Mailman\Event\EmailEvent;
use Mailman\Mailer\EmailLogger;
use Mailman\Test\TestCase\MailmanTestCase;

/**
 * Class EmailListenerTest
 *
 * @package Mailman\Test\TestCase\Event
 */
class EmailLoggerTest extends MailmanTestCase
{
    /**
     * @var EmailLogger
     */
    public $emailListener;

    /**
     * @var null|Message
     */
    public ?Message $email;

    /**
     * Setup
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->email = new Message([
            'from' => 'test@example.org',
            'to' => 'foo@example.org',
            'subject' => 'Test',
        ]);

        $this->emailListener = new EmailLogger();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
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
        $event = new EmailEvent('Email.beforeSend', $this->email);
        $this->emailListener->beforeSend($event);
    }

    /**
     * Test beforeSend method
     */
    public function testAfterSend()
    {
        $event = new EmailEvent('Email.afterSend', $this->email);
        $this->emailListener->afterSend($event);
    }
}
