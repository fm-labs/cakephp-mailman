<?php
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
    public function initialize(array $config)
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('folder');

        $validator
            ->requirePresence('transport', 'create')
            ->notEmpty('transport');

        $validator
            ->requirePresence('from', 'create')
            ->notEmpty('from');

        $validator
            ->allowEmpty('sender');

        $validator
            ->requirePresence('to', 'create')
            ->notEmpty('to');

        $validator
            ->allowEmpty('cc');

        $validator
            ->allowEmpty('bcc');

        $validator
            ->allowEmpty('reply_to');

        $validator
            ->allowEmpty('subject');

        $validator
            ->allowEmpty('headers');

        $validator
            ->allowEmpty('message');

        $validator
            ->allowEmpty('read_receipt');

        $validator
            ->allowEmpty('return_path');

        $validator
            ->allowEmpty('email_format');

        $validator
            ->allowEmpty('charset');

        $validator
            ->allowEmpty('result_headers');

        $validator
            ->allowEmpty('result_message');

        $validator
            ->add('error_code', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('error_code');

        $validator
            ->allowEmpty('error_msg');

        $validator
            ->add('sent', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('sent');

        $validator
            ->add('date_delivery', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('date_delivery');

        $validator
            ->allowEmpty('messageid');

        return $validator;
    }
}
