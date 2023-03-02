<?php // $this->Breadcrumbs->add(__d('mailman', 'New {0}', __d('mailman', 'Email Message'))); ?>
<?php $this->assign('title', __d('mailman', 'Compose Email')); ?>
<?php $this->extend('Admin./Base/form'); ?>
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
    <?= $this->Form->button(__d('mailman', 'Submit')) ?>
    <?= $this->Form->end() ?>

</div>