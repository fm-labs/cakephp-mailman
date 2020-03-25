<?php
declare(strict_types=1);

namespace Mailman\Controller\Admin;

/**
 * Class MailmanController
 *
 * @package Mailman\Controller\Admin
 */
class MailmanController extends AppController
{
    /**
     * Index action method
     */
    public function index()
    {
        $this->redirect(['controller' => 'EmailMessages', 'action' => 'index']);
    }
}
