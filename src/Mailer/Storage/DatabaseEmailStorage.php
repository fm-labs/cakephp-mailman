<?php
declare(strict_types=1);

namespace Mailman\Mailer\Storage;

use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\Message;
use Cake\ORM\TableRegistry;
use Mailman\Event\EmailEvent;

/**
 * Class DatabaseEmailStorage
 *
 * @package Mailman\Mailer\Storage
 */
class DatabaseEmailStorage implements EventListenerInterface
{
    use InstanceConfigTrait;

    /**
     * @var array
     */
    protected array $_defaultConfig = [
        'model' => 'Mailman.EmailMessages',
    ];

    /**
     * @var \Cake\ORM\Table
     */
    protected \Cake\ORM\Table $_table;

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
     * @param Email|Message $email
     * @param null $transport
     * @return bool|EntityInterface|mixed
     */
    public function store($email, $transport = null)
    {
        /** @var \Cake\Mailer\Message $msg */
        $msg = $email;
        if ($email instanceof Email) {
            $msg = $email->getMessage();
        }

        $transportResult = $transport['result'] ?? [];
        $transportClassName = $transport['transportClassName'] ?? null;


        $transportName = explode('\\', $transportClassName);
        $transportName = array_pop($transportName);
        $transportName = substr($transportName, 0, -strlen('Transport'));

        // Email instance to EmailMessage entity
        /** @var \Mailman\Model\Entity\EmailMessage $entity */
        $entity = $this->_table->newEntity([
            'charset'       => $msg->getCharset(),
            'subject'       => $msg->getOriginalSubject(),
            'from'          => $this->_listToString($msg->getFrom()),
            'sender'        => $this->_listToString($msg->getSender()),
            'to'            => $this->_listToString($msg->getTo()),
            'cc'            => $this->_listToString($msg->getCc()),
            'bcc'           => $this->_listToString($msg->getBcc()),
            'reply_to'      => $this->_listToString($msg->getReplyTo()),
            'read_receipt'  => $this->_listToString($msg->getReadReceipt()),
            'headers'       => $this->_listToString($msg->getHeaders(), true),
            'message'       => $msg->getBodyText(),
            'folder'        => 'sent',
            'error_code'    => 0,
            'error_message' => '',
            'sent'          => 0,
            'date_delivery' => null,
            'transport'     => $transportName,
        ]);


//        if ($msg->getTransport()) {
//            $transport = $msg->getTransport();
//            $transportPrefix = '';
//            if ($transport instanceof MailmanTransport && $transport->getOriginalTransport()) {
//                //$transportPrefix = "MailMan:";
//                $transport = $transport->getOriginalTransport();
//            } elseif (Plugin::isLoaded('DebugKit') && $transport instanceof DebugKitTransport) {
//                $transportPrefix = 'DebugKit:';
//                $reflection = new \ReflectionObject($transport);
//                $property = $reflection->getProperty('originalTransport');
//                $property->setAccessible(true);
//                $transport = $property->getValue($transport);
//
//                if ($transport instanceof MailmanTransport && $transport->getOriginalTransport()) {
//                    //$transportPrefix .= "MailMan:";
//                    $transport = $transport->getOriginalTransport();
//                }
//            }
//
//            $transportName = explode('\\', get_class($transport));
//            $transportName = array_pop($transportName);
//            $entity->transport = $transportPrefix . substr($transportName, 0, -strlen('Transport'));
//        }

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
            Log::alert(sprintf('Failed to store message in database'), ['mailman', 'email']);
            //Log::debug(json_encode($entity->getErrors()), ['mailman', 'email']);
        }

        return $result;
    }

    /**
     * @param \Mailman\Event\EmailEvent $event
     * @return void
     */
    public function afterEmailSend(EmailEvent $event)
    {
        try {
            $email = $event->getSubject();
            $transport = $event->getData();

            $this->store($email, $transport);
        } catch (\Exception $ex) {
            Log::error('[mailman][storage][db] Failed to store email message: ' . $ex->getMessage(), ['email']);
        }
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            //'Email.beforeSend' => 'beforeSend',
            'Email.afterSend' => 'afterEmailSend',
        ];
    }

    /**
     * @param $list
     * @param bool $withKeys
     * @param string $sep
     * @return string
     */
    protected function _listToString($list, bool $withKeys = false, string $sep = PHP_EOL)
    {
        if (is_string($list)) {
            return $list;
        }

        if ($withKeys) {
            $_list = [];
            array_walk($list, function ($val, $key) use (&$_list) {
                $_list[] = sprintf('%s: %s', $key, $val);
            });
            $list = $_list;
            unset($_list);
        }

        return join($sep, $list);
    }
}
