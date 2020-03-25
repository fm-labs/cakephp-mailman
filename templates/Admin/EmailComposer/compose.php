<?php // $this->Breadcrumbs->add(__('New {0}', __('Email Message'))); ?>
<?php $this->assign('title', __('Compose Email')); ?>
<?php $this->extend('Backend./Base/form'); ?>
<div class="form">
    <?= $this->Form->create($emailForm, ['class' => 'no-ajax', 'horizontal' => true]); ?>
    <?php
        echo $this->Form->control('from');
        echo $this->Form->control('to');
        echo $this->Form->control('cc');
        echo $this->Form->control('bcc');
        echo $this->Form->control('subject');
        echo $this->Form->control('message', ['rows' => 20]);
        echo $this->Form->control('log', ['default' => true]);
    ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>

</div>