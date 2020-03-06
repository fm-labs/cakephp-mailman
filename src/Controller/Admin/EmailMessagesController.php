<?php
namespace Mailman\Controller\Admin;

use Cake\Mailer\Email;

/**
 * EmailMessages Controller
 *
 * @property \Mailman\Model\Table\EmailMessagesTable $EmailMessages
 */
class EmailMessagesController extends AppController
{
    public $modelClass = "Mailman.EmailMessages";

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
        'index' => 'Backend.Index',
        'view' => 'Backend.View',
        'delete' => 'Backend.Delete',
    ];

    /**
     * Send Test email
     *
     * @return void
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

        $this->set([
            'paginate' => true,
            'sortable' => false,
            'filter' => false,
            'ajax' => false,
            'query' => [
                //'limit' => 25,
                'contain' => [],
                'fields' => ['id', 'subject', 'to', 'date_delivery', 'transport', 'sent', 'error_code', 'error_msg'],
                'order' => ['EmailMessages.id' => 'desc'],
            ],
            'fields' => [
                'date_delivery' => [],
                'to' => [],
                'subject' => [],
                'transport' => [],
                'sent' => [],
                'error_code' => []
            ]
        ]);

//        $this->Action->registerExternal('compose', [
//            'label' => 'Compose Message',
//            'url' =>  ['controller' => 'EmailComposer', 'action' => 'compose'],
//            'scope' => ['index']
//        ]);
        $this->Action->execute();
    }

    /**
     * View method
     *
     * @param string|null $id Email Message id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->set('fields', [
            'headers' => ['formatter' => function ($val) {
                return nl2br($val);
            }],
            'message' => ['formatter' => function ($val) {
                return '<textarea>' . h($val) . '</textarea>';
            }],
            'result_headers' => ['formatter' => function ($val) {
                return nl2br($val);
            }],
            'result_message' => ['formatter' => function ($val) {
                return '<textarea>' . h($val) . '</textarea>';
            }],
            'sent' => []
        ]);

        $this->Action->execute();
    }
}
