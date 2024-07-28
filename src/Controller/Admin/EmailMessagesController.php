<?php
declare(strict_types=1);

namespace Mailman\Controller\Admin;

/**
 * EmailMessages Controller
 *
 * @property \Mailman\Model\Table\EmailMessagesTable $EmailMessages
 * @property \Admin\Controller\Component\ActionComponent $Action
 */
class EmailMessagesController extends AppController
{
    //public $modelClass = 'Mailman.EmailMessages';

    public $defaultTable = 'Mailman.EmailMessages';

    /**
     * @var array
     */
    public $paginate = [
        'limit' => 100,
        'order' => ['EmailMessages.id' => 'DESC'],
    ];

    /**
     * @var array
     */
    public $actions = [
        'index' => 'Admin.Index',
        'view' => 'Admin.View',
        'delete' => 'Admin.Delete',
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $action = $this->Action->getAction('index');

        $this->paginate = [];

        $action->setVars([
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
                'error_code' => [],
            ],
        ]);

        $this->Action->registerExternal('compose', [
            'label' => 'Compose Message',
            'url' =>  ['controller' => 'EmailComposer', 'action' => 'compose'],
            'scope' => ['index']
        ]);

        $this->Action->dispatch($action);
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
            'sent' => [],
        ]);

        $this->Action->execute();
    }
}
