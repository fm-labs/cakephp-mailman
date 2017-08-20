<?php $this->Breadcrumbs->add(__('New {0}', __('Email Message'))); ?>
<?php $this->Toolbar->addLink(
    __('List {0}', __('Email Messages')),
    ['controller' => 'EmailMessages', 'action' => 'index'],
    ['data-icon' => 'list']
) ?>
<div class="form">
    <?= $this->Form->create($emailForm, ['class' => 'no-ajax']); ?>
    <?php
        echo $this->Form->input('from');
        echo $this->Form->input('to');
        echo $this->Form->input('cc');
        echo $this->Form->input('bcc');
        echo $this->Form->input('subject');
        echo $this->Form->input('message');
        echo $this->Form->input('log', ['default' => true]);
    ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>

</div>