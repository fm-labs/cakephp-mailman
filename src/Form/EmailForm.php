<?php
namespace Mailman\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Mailer\Email;
use Cake\Validation\Validator;

/**
 * Class EmailForm
 *
 * @package Mailman\Form
 */
class EmailForm extends Form
{
    /**
     * @param Schema $schema
     * @return $this
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('from', ['type' => 'string'])
            ->addField('to', ['type' => 'string'])
            ->addField('subject', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('log', ['type' => 'boolean']);
    }

    /**
     * @param Validator $validator
     * @return $this
     */
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

    /**
     * Send email
     *
     * @param array $data
     * @return array
     */
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

        return $email->send();
    }
}
