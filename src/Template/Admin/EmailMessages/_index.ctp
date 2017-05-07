<?php $this->Breadcrumbs->add(__('Email Messages')); ?>

<?php $this->Toolbar->addLink(__('New {0}', __('Email Message')), ['action' => 'add'], ['data-icon' => 'plus']); ?>
<?php $this->Toolbar->addLink(__('Send Testemail'), ['action' => 'test'], ['data-icon' => 'mail outline']); ?>
<div class="emailMessages index">
    <?= $this->cell('Backend.DataTable', [[
        'paginate' => true,
        'model' => 'Mailman.EmailMessages',
        'data' => $emailMessages,
        'fields' => [
            'id',
            'date_delivery',
            'folder',
            'transport',
            'from',
            'to',
            'subject' => [
                'formatter' => function($val, $row) {
                    return $this->Html->link($val, ['action' => 'view', $row->id]);
                }
            ]
        ],
        'rowActions' => [
            [__d('shop','View'), ['action' => 'view', ':id'], ['class' => 'view']],
            [__d('shop','Edit'), ['action' => 'edit', ':id'], ['class' => 'edit']],
            [__d('shop','Delete'), ['action' => 'delete', ':id'], ['class' => 'delete', 'confirm' => __d('shop','Are you sure you want to delete # {0}?', ':id')]]
        ]
    ]]);
    ?>
</div>
