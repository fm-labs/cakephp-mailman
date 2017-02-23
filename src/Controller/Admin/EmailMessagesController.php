<?php
namespace Mailman\Controller\Admin;

use Cake\Mailer\Email;
use Mailman\Email\MailmanMailer;

/**
 * EmailMessages Controller
 *
 * @property \Mailman\Model\Table\EmailMessagesTable $EmailMessages
 */
class EmailMessagesController extends AppController
{

    public $paginate = [
        'limit' => 100,
        'order' => ['EmailMessages.id' => 'DESC']
    ];

    public function test()
    {

        $email = new Email([
            'transport' => 'default',
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

        $mailer = new MailmanMailer();
        $mailer->sendEmail($email, true);

        $this->setAction('index');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {

        $this->set('emailMessages', $this->paginate($this->EmailMessages));
        $this->set('_serialize', ['emailMessages']);
    }

    /**
     * View method
     *
     * @param string|null $id Email Message id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $emailMessage = $this->EmailMessages->get($id, [
            'contain' => []
        ]);
        $this->set('emailMessage', $emailMessage);
        $this->set('_serialize', ['emailMessage']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $emailMessage = $this->EmailMessages->newEntity();
        if ($this->request->is('post')) {
            $emailMessage = $this->EmailMessages->patchEntity($emailMessage, $this->request->data);
            if ($this->EmailMessages->save($emailMessage)) {
                $this->Flash->success(__('The {0} has been saved.', __('email message')));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The {0} could not be saved. Please, try again.', __('email message')));
            }
        }
        $this->set(compact('emailMessage'));
        $this->set('_serialize', ['emailMessage']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Email Message id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $emailMessage = $this->EmailMessages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $emailMessage = $this->EmailMessages->patchEntity($emailMessage, $this->request->data);
            if ($this->EmailMessages->save($emailMessage)) {
                $this->Flash->success(__('The {0} has been saved.', __('email message')));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The {0} could not be saved. Please, try again.', __('email message')));
            }
        }
        $this->set(compact('emailMessage'));
        $this->set('_serialize', ['emailMessage']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Email Message id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $emailMessage = $this->EmailMessages->get($id);
        if ($this->EmailMessages->delete($emailMessage)) {
            $this->Flash->success(__('The {0} has been deleted.', __('email message')));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', __('email message')));
        }
        return $this->redirect(['action' => 'index']);
    }
}
