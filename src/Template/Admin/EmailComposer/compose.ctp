<?php // $this->Breadcrumbs->add(__('New {0}', __('Email Message'))); ?>
<?php $this->assign('title', __('Compose Email')); ?>
<?php $this->extend('Backend./Base/form'); ?>
<div class="form">
    <?= $this->Form->create($emailForm, ['class' => 'no-ajax', 'horizontal' => true]); ?>
    <?php
        echo $this->Form->input('from');
        echo $this->Form->input('to');
        echo $this->Form->input('cc');
        echo $this->Form->input('bcc');
        echo $this->Form->input('subject');
        echo $this->Form->input('message', ['rows' => 20]);
        echo $this->Form->input('log', ['default' => true]);
    ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>

</div>