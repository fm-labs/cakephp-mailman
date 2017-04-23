<?php $this->Breadcrumbs->add(__('Email Messages'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('New {0}', __('Email Message'))); ?>
<?php $this->Toolbar->addLink(
    __('List {0}', __('Email Messages')),
    ['controller' => 'EmailMessages', 'action' => 'index'],
    ['data-icon' => 'list']
) ?>
<div class="form">
    <h2 class="ui header">
        <?= __('Compose Email') ?>
    </h2>
    <?= $this->Form->create($emailForm, ['class' => 'no-ajax']); ?>
    <div class="users ui basic segment">
        <div class="ui form">
        <?php
            echo $this->Form->input('from');
            echo $this->Form->input('to');
            echo $this->Form->input('cc');
            echo $this->Form->input('bcc');
            echo $this->Form->input('subject');
            echo $this->Form->input('message');
            echo $this->Form->input('log', ['default' => true]);
        ?>
        </div>
    </div>
    <div class="ui bottom attached segment">
        <?= $this->Form->button(__('Submit')) ?>
    </div>
    <?= $this->Form->end() ?>

</div>