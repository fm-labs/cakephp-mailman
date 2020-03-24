<?php

namespace Mailman\Controller\Admin;

use Mailman\Form\EmailForm;

/**
 * Class EmailComposerController
 *
 * @package Mailman\Controller\Admin
 */
class EmailComposerController extends AppController
{
    /**
     * Index action method
     */
    public function index()
    {
        $this->redirect(['action' => 'compose']);
    }

    /**
     * Compose email action
     */
    public function compose()
    {
        $form = new EmailForm();
        if ($this->request->is(['post', 'put'])) {
            try {
                $result = $form->execute($this->request->getData());
                debug($result);
                $this->Flash->success(__('Email message has been sent'));
            } catch (\Exception $ex) {
                $this->Flash->error($ex->getMessage());
            }
        }

        $this->set('emailForm', $form);
    }
}
