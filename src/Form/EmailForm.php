<?php
declare(strict_types=1);

namespace Mailman\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Mailman\Mailer\MailmanMailer;

/**
 * Class EmailForm
 *
 * @package Mailman\Form
 */
class EmailForm extends Form
{
    /**
     * @param \Cake\Form\Schema $schema
     * @return $this
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('profile', ['type' => 'string'])
            ->addField('from', ['type' => 'string'])
            ->addField('to', ['type' => 'string'])
            ->addField('subject', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('log', ['type' => 'boolean']);
    }

    /**
     * @param \Cake\Validation\Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->add('profile', 'notblank', [
                'rule' => 'notBlank',
            ])
            ->add('transport', 'notblank', [
                'rule' => 'notBlank',
            ])
            ->add('from', 'notblank', [
                'rule' => 'notBlank',
            ])
            ->add('to', 'notblank', [
                'rule' => 'notBlank',
            ])
            ->add('subject', 'notblank', [
                'rule' => 'notBlank',
            ])
            ->add('message', 'notblank', [
                'rule' => 'notBlank',
            ]);
    }

    /**
     * Send email
     *
     * @param array $data Form data
     * @return bool
     */
    protected function _execute(array $data): bool
    {
        try {
            $profile = $data['profile'] ?? 'default';
            $mailer = new MailmanMailer($profile);
            $mailer->setProfile([
                'transport' => $data['transport'],
                'from' => $data['from'],
                'to' => $data['to'],
                'subject' => $data['subject'],
                'template' => null,
                'layout' => null,
                'log' => (bool)$data['log'],
                //'from' => $data['from'],
                //'to' => $data['to'],
                //'subject' => $data['subject'],
            ]);
            $mailer->deliver($data['message']);
//            $mailer = new MailmanMailer($profile);
//            $options = [
//                'transport' => $data['transport'],
//                'from' => $data['from'],
//                'to' => $data['to'],
//                'subject' => $data['subject'],
//            ];
//            $message = $data['message'];
//            $mailer->send('composed', [$options, $message]);
        } catch (\Exception $ex) {
            debug($ex->getMessage());
            //return false;
            throw $ex;
        }

        return true;
    }
}
