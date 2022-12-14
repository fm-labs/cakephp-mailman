<?php
declare(strict_types=1);

namespace Mailman\Controller\Admin;

use Cake\Http\Response;
use Mailman\Mailer\MailmanMailer;

/**
 * Class MailmanController
 *
 * @package Mailman\Controller\Admin
 */
class MailmanController extends AppController
{
    /**
     * Index action method
     *
     * @return null|\Cake\Http\Response
     */
    public function index(): ?Response
    {
        return $this->redirect(['controller' => 'EmailMessages', 'action' => 'index']);
    }

    /**
     * Send Test email
     *
     * @return void
     */
    public function test()
    {
        $mailer = new MailmanMailer([
            'transport' => 'debug',
            'from' => 'tester@example.org',
            'to' => ['mailtest@example.org'],
            'cc' => ['mailtest@example.org'],
            'bcc' => ['mailtest@example.org'],
            'sender' => ['mailtest@example.org'],
            'replyTo' => ['mailtest@example.org'],
            'readReceipt' => ['mailtest@example.org'],
            'subject' => 'Test',
            'template' => false,
            'layout' => false,
            'log' => true,
        ]);

        $result = null;
        try {
            $result = $mailer->send();
            $this->Flash->success(__('Email has been sent.'));
        } catch (\Exception $ex) {
            $this->Flash->error(__('Failed to send email: {0}', $ex->getMessage()));
        }

        $this->set('result', $result);
        $this->setAction('index');
    }
}
