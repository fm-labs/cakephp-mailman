<?php

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
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        $this->loadComponent('Backend.Backend');
        $this->loadComponent('Backend.Action');
    }
}
