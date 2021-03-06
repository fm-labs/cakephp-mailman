<?php
declare(strict_types=1);

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
     * @param \Cake\Form\Schema $schema
     * @return $this
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('from', ['type' => 'string'])
            ->addField('to', ['type' => 'string'])
            ->addField('subject', ['type' => 'string'])
            ->addField('message', ['type' => 'text'])
            ->addField('log', ['type' => 'boolean']);
    }

    /**
     * @param \Cake\Validation\Validator $validator
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
    protected function _execute(array $data): bool
    {
        $email = new Email([
            'transport' => 'default',
            'from' => $data['from'],
            'to' => $data['to'],
            'subject' => $data['subject'],
            'template' => false,
            'layout' => false,
            'log' => $data['log'],
        ]);

        return $email->send();
    }
}
