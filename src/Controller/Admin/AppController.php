<?php
declare(strict_types=1);

namespace Mailman\Controller\Admin;

use Cake\Controller\Controller;

/**
 * Class AppController
 *
 * @package Mailman\Controller\Admin
 */
class AppController extends Controller
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->loadComponent('Admin.Admin');
        $this->loadComponent('Admin.Action');
    }
}
