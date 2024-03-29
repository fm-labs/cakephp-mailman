<?php
declare(strict_types=1);

namespace Mailman\Test\TestCase\Mailer\Transport;

use Cake\Event\Event;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\Mailer\Message;
use Mailman\Test\TestCase\MailmanTestCase;

/**
 * Class MailmanTransportTest
 *
 * @package Mailman\Test\TestCase\Mailer\Transport
 */
class MailmanTransportTest extends MailmanTestCase
{
    /**
     * @var null|Message
     */
    public ?Message $email;

    /**
     * Setup test class
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->email = new Message([
            'from' => 'test@example.org',
            'to' => 'foo@example.org',
            'subject' => 'Test',
        ]);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->email = null;
    }

    /**
     * Test beforeSend event
     */
    public function testBeforeSendEvent()
    {
        $result = [];
        EventManager::instance()->setEventList(new EventList());
        EventManager::instance()->addEventToList(new Event('Email.beforeSend'));
        EventManager::instance()->on('Email.beforeSend', function (Event $event) use (&$result) {
            $result['email'] = $event->getSubject();
        });

        //$this->email->send();
        $this->markTestIncomplete();

        $this->assertEventFired('Email.beforeSend');
        $this->assertArrayHasKey('email', $result);
        $this->assertInstanceOf(Message::class, $result['email']);
    }

    /**
     * Test afterSend event
     */
    public function testAfterSendEvent()
    {
        $result = [];
        EventManager::instance()->setEventList(new EventList());
        EventManager::instance()->addEventToList(new Event('Email.afterSend'));
        EventManager::instance()->on('Email.afterSend', function (Event $event) use (&$result) {
            $result['email'] = $event->getSubject();
            $result['data'] = $event->getData();
        });

        //$this->email->send();
        $this->markTestIncomplete();

        $this->assertEventFired('Email.afterSend');
        $this->assertArrayHasKey('email', $result);
        $this->assertInstanceOf(Message::class, $result['email']);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('headers', $result['data']);
        $this->assertArrayHasKey('message', $result['data']);
    }

    /**
     * Test transportError event
     */
    public function testTransportErrorEvent()
    {

        $this->markTestIncomplete('Emulate email transport error');

        $result = [];
        EventManager::instance()->setEventList(new EventList());
        EventManager::instance()->addEventToList(new Event('Email.transportError'));
        EventManager::instance()->on('Email.transportError', function (Event $event) use (&$result) {
            $result['email'] = $event->getSubject();
            $result['data'] = $event->getData();
        });

        //$this->email->send();
        $this->markTestIncomplete();

        $this->assertEventFired('Email.transportError');
        $this->assertArrayHasKey('email', $result);
        $this->assertInstanceOf(Message::class, $result['email']);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('headers', $result['data']);
        $this->assertArrayHasKey('message', $result['data']);
        $this->assertArrayHasKey('error', $result['data']);
    }
}
