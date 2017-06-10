<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 3/6/16
 * Time: 8:47 PM
 */

namespace Mailman\Controller\Admin;

use Mailman\Form\EmailForm;

class EmailComposerController extends AppController
{
    public function index()
    {
        $this->redirect(['action' => 'compose']);
    }

    public function compose()
    {
        $form = new EmailForm();
        if ($this->request->is(['post', 'put'])) {
            try {
                $result = $form->execute($this->request->data);
                debug($result);
                $this->Flash->success(__('Email message has been sent'));
            } catch (\Exception $ex) {
                $this->Flash->error($ex->getMessage());
            }
        }

        $this->set('emailForm', $form);
    }
}
