<?php $this->Breadcrumbs->add(__('Email Messages'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('Edit {0}', __('Email Message'))); ?>
<?= $this->Toolbar->addPostLink(
    __('Delete'),
    ['action' => 'delete', $emailMessage->id],
    ['data-icon' => 'trash', 'confirm' => __('Are you sure you want to delete # {0}?', $emailMessage->id)]
)
?>
<?= $this->Toolbar->addLink(
    __('List {0}', __('Email Messages')),
    ['action' => 'index'],
    ['data-icon' => 'list']
) ?>
<?php $this->Toolbar->startGroup('More'); ?>
<?php $this->Toolbar->endGroup(); ?>
<div class="form">
    <h2 class="ui header">
        <?= __('Edit {0}', __('Email Message')) ?>
    </h2>
    <?= $this->Form->create($emailMessage); ?>
    <div class="users ui basic segment">
        <div class="ui form">
        <?php
                echo $this->Form->input('folder');
                echo $this->Form->input('transport');
                echo $this->Form->input('from');
                echo $this->Form->input('sender');
                echo $this->Form->input('to');
                echo $this->Form->input('cc');
                echo $this->Form->input('bcc');
                echo $this->Form->input('reply_to');
                echo $this->Form->input('subject');
                echo $this->Form->input('headers');
                echo $this->Form->input('message');
                echo $this->Form->input('read_receipt');
                echo $this->Form->input('return_path');
                echo $this->Form->input('email_format');
                echo $this->Form->input('charset');
                echo $this->Form->input('result_headers');
                echo $this->Form->input('result_message');
                echo $this->Form->input('error_code');
                echo $this->Form->input('error_msg');
                echo $this->Form->input('sent');
                //echo $this->Form->input('date_delivery');
                echo $this->Form->input('messageid');
        ?>
        </div>
    </div>
    <div class="ui bottom attached segment">
        <?= $this->Form->button(__('Submit')) ?>
    </div>
    <?= $this->Form->end() ?>

</div>