<?php $this->Breadcrumbs->add(__('Email Messages'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($emailMessage->subject); ?>
<?= $this->Toolbar->addLink(
    __('Edit {0}', __('Email Message')),
    ['action' => 'edit', $emailMessage->id],
    ['data-icon' => 'edit']
) ?>
<?= $this->Toolbar->addLink(
    __('Delete {0}', __('Email Message')),
    ['action' => 'delete', $emailMessage->id],
    ['data-icon' => 'trash', 'confirm' => __('Are you sure you want to delete # {0}?', $emailMessage->id)]) ?>

<?= $this->Toolbar->addLink(
    __('List {0}', __('Email Messages')),
    ['action' => 'index'],
    ['data-icon' => 'list']
) ?>
<?= $this->Toolbar->addLink(
    __('New {0}', __('Email Message')),
    ['action' => 'add'],
    ['data-icon' => 'plus']
) ?>
<?= $this->Toolbar->startGroup(__('More')); ?>
<?= $this->Toolbar->endGroup(); ?>
<div class="emailMessages view">
    <h2 class="ui header">
        <?= h($emailMessage->subject) ?>
    </h2>
    <table class="ui attached celled striped table">
        <!--
        <thead>
        <tr>
            <th><?= __('Label'); ?></th>
            <th><?= __('Value'); ?></th>
        </tr>
        </thead>
        -->

        <tr>
            <td><?= __('Folder') ?></td>
            <td><?= h($emailMessage->folder) ?></td>
        </tr>
        <tr>
            <td><?= __('Transport') ?></td>
            <td><?= h($emailMessage->transport) ?></td>
        </tr>
        <tr>
            <td><?= __('From') ?></td>
            <td><?= h($emailMessage->from) ?></td>
        </tr>
        <tr>
            <td><?= __('Sender') ?></td>
            <td><?= h($emailMessage->sender) ?></td>
        </tr>
        <tr>
            <td><?= __('Reply To') ?></td>
            <td><?= h($emailMessage->reply_to) ?></td>
        </tr>
        <tr>
            <td><?= __('Subject') ?></td>
            <td><?= h($emailMessage->subject) ?></td>
        </tr>
        <tr>
            <td><?= __('Return Path') ?></td>
            <td><?= h($emailMessage->return_path) ?></td>
        </tr>
        <tr>
            <td><?= __('Email Format') ?></td>
            <td><?= h($emailMessage->email_format) ?></td>
        </tr>
        <tr>
            <td><?= __('Charset') ?></td>
            <td><?= h($emailMessage->charset) ?></td>
        </tr>
        <tr>
            <td><?= __('Error Msg') ?></td>
            <td><?= h($emailMessage->error_msg) ?></td>
        </tr>
        <tr>
            <td><?= __('Messageid') ?></td>
            <td><?= h($emailMessage->messageid) ?></td>
        </tr>


        <tr>
            <td><?= __('Id') ?></td>
            <td><?= $this->Number->format($emailMessage->id) ?></td>
        </tr>
        <tr>
            <td><?= __('Error Code') ?></td>
            <td><?= $this->Number->format($emailMessage->error_code) ?></td>
        </tr>


        <tr class="date">
            <td><?= __('Date Delivery') ?></td>
            <td><?= h($emailMessage->date_delivery) ?></td>
        </tr>
        <tr class="date">
            <td><?= __('Created') ?></td>
            <td><?= h($emailMessage->created) ?></td>
        </tr>

        <tr class="boolean">
            <td><?= __('Read Receipt') ?></td>
            <td><?= $emailMessage->read_receipt ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr class="boolean">
            <td><?= __('Sent') ?></td>
            <td><?= $emailMessage->sent ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('To') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->to)); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('Cc') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->cc)); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('Bcc') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->bcc)); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('Headers') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->headers)); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('Message') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->message)); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('Result Headers') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->result_headers)); ?></td>
        </tr>
        <tr class="text">
            <td><?= __('Result Message') ?></td>
            <td><?= $this->Text->autoParagraph(h($emailMessage->result_message)); ?></td>
        </tr>
    </table>
</div>
