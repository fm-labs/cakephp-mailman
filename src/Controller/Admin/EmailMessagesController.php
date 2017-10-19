<?php
namespace Mailman\Controller\Admin;

use Backend\Controller\BackendActionsTrait;
use Cake\Mailer\Email;

/**
 * EmailMessages Controller
 *
 * @property \Mailman\Model\Table\EmailMessagesTable $EmailMessages
 */
class EmailMessagesController extends AppController
{
    use BackendActionsTrait;

    /**
     * @var array
     */
    public $paginate = [
        'limit' => 100,
        'order' => ['EmailMessages.id' => 'DESC']
    ];

    /**
     * @var array
     */
    public $actions = [
        //'indexfoo'  => 'Backend.FooTableIndex',
        //'indexdt'   => 'Backend.DataTableIndex',
        'index'     => 'Backend.FooTableIndex',
        'view'      => 'Backend.View',
        //'add'       => false,
        'edit'      => 'Backend.Edit',
        'delete'    => 'Backend.Delete',
    ];

    /**
     * Send Test email
     * @TODO Refactor with email form or move to tests
     */
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

        if (!$email->send()) {
            $this->Flash->success(__('Email has been sent.'));
        } else {
            $this->Flash->error(__('Failed to send email.'));
        }

        $this->setAction('index');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [];

        //$this->set('paginate', true);
        //$this->set('sortable', true);
        //$this->set('filter', true);
        //$this->set('ajax', true);
        //$this->set('order', ['id' => 'desc']);

        $this->set([
            'paginate' => true,
            'sortable' => true,
            'filter' => false,
            'ajax' => true,
            'query' => [
                //'limit' => 25
                'order' => ['EmailMessages.id' => 'desc']
            ]
        ]);
        $this->set('fields.whitelist', [
            'id',
            'folder',
            'subject',
            'to',
            'from',
            'transport',
            'date_delivery',
        ]);
        $this->set('actions', [
            'compose' => [__('Compose Email'), ['controller' => 'EmailComposer', 'action' => 'compose'], ['data-icon' => 'envelope-o']]
        ]);
        $this->Action->execute();
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index2()
    {
        $this->set('fields.whitelist', [
            'id',
            'folder',
            'subject',
            'to',
            'from',
            'transport',
            'date_delivery',
        ]);
        $this->set('actions', [
            'compose' => [__('Compose Email'), ['controller' => 'EmailComposer', 'action' => 'compose'], ['data-icon' => 'envelope-o']]
        ]);
        $this->Action->execute();
        $this->render('Backend.index');
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
        $this->set('fields', [
            'headers' => ['formatter' => function ($val) {
                return nl2br($val);
            }],
            'message' => ['formatter' => function ($val) {
                return nl2br($val);
            }],
            'result_headers' => ['formatter' => function ($val) {
                return nl2br($val);
            }],
            'result_message' => ['formatter' => function ($val) {
                return nl2br($val);
            }],
        ]);

        $this->Action->execute();
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     * @todo Use Backend Action instead
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
    */

    /**
     * Edit method
     *
     * @param string|null $id Email Message id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     * @todo Use Backend Action instead
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
     */
    public function delete($id = null)
    {
        $this->Action->execute();
    }
}
