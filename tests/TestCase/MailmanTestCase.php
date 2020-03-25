<?php
declare(strict_types=1);

namespace Mailman\Test\TestCase;

use Cake\Mailer\TransportFactory;
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
    public function setUp(): void
    {
        parent::setUp();

        TransportFactory::setConfig('test', [
            'className' => 'Mailman.Mailman',
            'originalClassName' => 'Debug',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        TransportFactory::drop('test');
    }
}
