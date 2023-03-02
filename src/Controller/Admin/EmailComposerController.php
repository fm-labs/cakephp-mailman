<?php
declare(strict_types=1);

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
     *
     * @return void
     */
    public function index(): void
    {
        $this->redirect(['action' => 'compose']);
    }

    /**
     * Compose email action
     *
     * @return void
     */
    public function compose(): void
    {
        $form = new EmailForm();
        $result = null;
        if ($this->request->is(['post', 'put'])) {
            try {
                $result = $form->execute($this->request->getData());
                $this->Flash->success(__d('mailman', 'Email message has been sent'));
            } catch (\Exception $ex) {
                $this->Flash->error($ex->getMessage());
            }
        }

        $this->set('emailForm', $form);
        $this->set('result', $result);
    }
}
