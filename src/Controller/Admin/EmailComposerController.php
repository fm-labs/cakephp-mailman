<?php
declare(strict_types=1);

namespace Mailman\Controller\Admin;

use Cake\Mailer\Mailer;
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
        $profile = $this->request->getQuery('profile', 'default');

        $form = new EmailForm();
        $result = null;
        if ($this->request->is(['post', 'put'])) {
            try {
                $form->execute($this->request->getData());
                $this->Flash->success(__d('mailman', 'Email message has been sent'));
            } catch (\Exception $ex) {
                $this->Flash->error($ex->getMessage());
            }
        } else {
            $config = Mailer::getConfig($profile);
            $config['profile'] = $profile;
            $this->request = $this->request->withParsedBody($config);
        }

        $profileKeys = Mailer::configured();
        $this->set('profiles', $profileKeys);

        $this->set('emailForm', $form);
        $this->set('result', $result);
    }
}
