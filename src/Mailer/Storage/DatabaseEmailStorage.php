<?php

namespace Mailman\Mailer\Storage;

use Cake\Core\InstanceConfigTrait;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;

/**
 * Class DatabaseEmailStorage
 *
 * @package Mailman\Mailer\Storage
 * @todo Refactor as Log engine
 */
class DatabaseEmailStorage
{
    use InstanceConfigTrait;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'model' => 'Mailman.EmailMessages'
    ];

    /**
     * @var \Cake\ORM\Table
     */
    protected $_table;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config($config);
        $this->_table = TableRegistry::get($this->config('model'));
    }

    /**
     * Store Email instance in database
     *
     * @param Email $email
     * @param null $transportResult
     * @return bool|\Cake\Datasource\EntityInterface|mixed
     */
    public function store(Email $email, $transportResult = null)
    {
        // Email instance to EmailMessage entity
        $entity = $this->_table->newEntity([
            'folder' => 'sent',
            'from' => $this->_listToString($email->from()),
            'sender' => $this->_listToString($email->sender()),
            'to' => $this->_listToString($email->to()),
            'cc' => $this->_listToString($email->cc()),
            'bcc' => $this->_listToString($email->bcc()),
            'reply_to' => $this->_listToString($email->replyTo()),
            'read_receipt' => $this->_listToString($email->readReceipt()),
            'subject' => $email->subject(),
            'headers' => $this->_listToString($email->getHeaders(), true),
            'message' => $this->_listToString($email->message()),
            'charset' => $email->charset(),
            'error_code' => 0,
            'error_message' => '',
            'sent' => 0,
            'date_delivery' => null

        ]);

        if ($email->transport()) {
            $transport = explode('\\', get_class($email->transport()));
            $transport = array_pop($transport);
            $entity->transport = substr($transport, 0, -strlen('Transport'));
        }

        if (!is_array($transportResult) || empty($transportResult)) {
            $entity->folder = 'outbox';
            $entity->error_code = 1;
            $entity->result_headers = '';
            $entity->result_message = '';

        } elseif (isset($transportResult['error'])) {
            $entity->folder = 'outbox';
            $entity->error_code = 1;
            $entity->error_message = $transportResult['error'];
            $entity->sent = 0;
            $entity->date_delivery = null;
            //$entity->result_headers = $transportResult['headers'];
            $entity->result_message = $transportResult['error'];

        } else {
            $entity->folder = 'sent';
            $entity->error_code = 0;
            $entity->error_msg = '';
            $entity->sent = 1;
            $entity->date_delivery = new \DateTime();
            $entity->result_headers = $transportResult['headers'];
            $entity->result_message = $transportResult['message'];
        }

        $result = $this->_table->save($entity);
        if (!$result) {
            Log::alert(sprintf("Failed to store message in database"), ['mailman', 'email']);
        }

        return $result;
    }

    /**
     * @param $list
     * @param bool|false $withKeys
     * @param string $sep
     * @return array|string
     */
    protected function _listToString($list, $withKeys = false, $sep = PHP_EOL)
    {
        if (is_string($list)) {
            return $list;
        }

        if ($withKeys) {
            $_list = [];
            array_walk($list, function ($val, $key) use (&$_list) {
                $_list[] = sprintf("%s: %s", $key, $val);
            });
            $list = $_list;
            unset($_list);
        }

        return join($sep, $list);
    }
}
