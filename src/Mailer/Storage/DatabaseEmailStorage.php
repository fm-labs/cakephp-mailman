<?php

namespace Mailman\Mailer\Storage;

use Cake\Core\InstanceConfigTrait;
use Cake\Core\Plugin;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use DebugKit\Mailer\Transport\DebugKitTransport;
use Mailman\Mailer\Transport\MailmanTransport;
use Mailman\Model\Entity\EmailMessage;

/**
 * Class DatabaseEmailStorage
 *
 * @package Mailman\Mailer\Storage
 */
class DatabaseEmailStorage
{
    use InstanceConfigTrait;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'model' => 'Mailman.EmailMessages',
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
        $this->setConfig($config);
        $this->_table = TableRegistry::getTableLocator()->get($this->getConfig('model'));
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
        /** @var EmailMessage $entity */
        $entity = $this->_table->newEntity([
            'charset'       => $email->getCharset(),
            'subject'       => $email->getOriginalSubject(),
            'from'          => $this->_listToString($email->getFrom()),
            'sender'        => $this->_listToString($email->getSender()),
            'to'            => $this->_listToString($email->getTo()),
            'cc'            => $this->_listToString($email->getCc()),
            'bcc'           => $this->_listToString($email->getBcc()),
            'reply_to'      => $this->_listToString($email->getReplyTo()),
            'read_receipt'  => $this->_listToString($email->getReadReceipt()),
            'headers'       => $this->_listToString($email->getHeaders(), true),
            'message'       => $this->_listToString($email->message()),
            'folder'        => 'sent',
            'error_code'    => 0,
            'error_message' => '',
            'sent'          => 0,
            'date_delivery' => null,
        ]);

        if ($email->getTransport()) {
            $transport = $email->getTransport();
            $transportPrefix = "";
            if ($transport instanceof MailmanTransport && $transport->getOriginalTransport()) {
                //$transportPrefix = "MailMan:";
                $transport = $transport->getOriginalTransport();
            } elseif (Plugin::isLoaded('DebugKit') && $transport instanceof DebugKitTransport) {
                $transportPrefix = "DebugKit:";
                $reflection = new \ReflectionObject($transport);
                $property = $reflection->getProperty('originalTransport');
                $property->setAccessible(true);
                $transport = $property->getValue($transport);

                if ($transport instanceof MailmanTransport && $transport->getOriginalTransport()) {
                    //$transportPrefix .= "MailMan:";
                    $transport = $transport->getOriginalTransport();
                }
            }

            $transportName = explode('\\', get_class($transport));
            $transportName = array_pop($transportName);
            $entity->transport = $transportPrefix . substr($transportName, 0, -strlen('Transport'));
        }

        if (!is_array($transportResult) || empty($transportResult)) {
            $entity->folder = 'outbox';
            $entity->error_code = 1; // Malformed or empty transport result
            $entity->error_msg = 'Malformed Result';
            $entity->result_headers = '';
            $entity->result_message = '';
        } elseif (isset($transportResult['error'])) {
            $entity->folder = 'outbox';
            $entity->error_code = 2; // Transport error
            $entity->error_msg = $transportResult['error'];
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
