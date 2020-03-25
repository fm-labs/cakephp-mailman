<?php
declare(strict_types=1);

namespace Mailman\Model\Table;

use Cake\Core\Plugin;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Class EmailMessagesTable
 *
 * @package Mailman\Model\Table
 */
class EmailMessagesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mailman_email_messages');
        $this->setDisplayField('subject');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        if (Plugin::isLoaded('Search')) {
            $this->addBehavior('Search.Search');
            $this->searchManager()
                ->add('q', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['to', 'from', 'subject'],
                ])
                ->add('from', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['from'],
                ])
                ->add('to', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['to'],
                ])
                ->add('subject', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['subject'],
                ])
                ->value('folder', [
                    'filterEmpty' => true,
                ])
                ->value('transport', [
                    'filterEmpty' => true,
                ]);
        }
    }

    /**
     * @param $field
     */
    public function sources($field)
    {
        switch ($field) {
            //@TODO Implement me
        }
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyString('folder');

        $validator
            ->requirePresence('transport', 'create')
            ->notEmptyString('transport');

        $validator
            ->requirePresence('from', 'create')
            ->notEmptyString('from');

        $validator
            ->allowEmptyString('sender');

        $validator
            ->requirePresence('to', 'create')
            ->notEmptyString('to');

        $validator
            ->allowEmptyString('cc');

        $validator
            ->allowEmptyString('bcc');

        $validator
            ->allowEmptyString('reply_to');

        $validator
            ->allowEmptyString('subject');

        $validator
            ->allowEmptyString('headers');

        $validator
            ->allowEmptyString('message');

        $validator
            ->allowEmptyString('read_receipt');

        $validator
            ->allowEmptyString('return_path');

        $validator
            ->allowEmptyString('email_format');

        $validator
            ->allowEmptyString('charset');

        $validator
            ->allowEmptyString('result_headers');

        $validator
            ->allowEmptyString('result_message');

        $validator
            ->add('error_code', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('error_code');

        $validator
            ->allowEmptyString('error_msg');

        $validator
            ->add('sent', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('sent');

        $validator
            ->add('date_delivery', 'valid', ['rule' => 'datetime'])
            ->allowEmptyString('date_delivery');

        $validator
            ->allowEmptyString('messageid');

        return $validator;
    }
}
