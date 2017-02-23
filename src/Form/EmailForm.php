<?php
namespace Mailman\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Mailman\Email\MailmanMailer;

class EmailForm extends Form
{

    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('from', ['type' => 'string'])
            ->addField('to', ['type' => 'string'])
            ->addField('subject', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('log', ['type' => 'boolean']);
    }

    protected function _buildValidator(Validator $validator)
    {
        return $validator

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

    protected function _execute(array $data)
    {


        $email = new Email([
            'transport' => 'default',
            'from' => $data['from'],
            'to' => $data['to'],
            'subject' => $data['subject'],
            'template' => false,
            'layout' => false,
            'log' => true,
        ]);

        $mailer = new MailmanMailer();
        return $mailer->sendEmail($email, $data['message'], true);
    }
}