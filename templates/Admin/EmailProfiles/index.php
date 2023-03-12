<?php
$this->loadHelper('Admin.DataTable')
?>
<div class="index container-fluid">
    <div class="mb-3">
        <?php
        $profiles = $this->get('profiles', []);
        $dtOptions = [
            'modelClass' => false,
            'fields' => [
                'key' => [],
                'transport' => [],
                'from' => [],
                'to' => [],
                'cc' => [],
                'bcc' => [],
                'theme' => [],
            ]
        ];
        $dt = $this->DataTable->create($dtOptions, $profiles);
        echo $dt->render();
        ?>
    </div>

    <div class="mb-3">
        <h3>Email Transports</h3>
        <hr/>
        <?php
        $transports = $this->get('transports', []);
        $dtOptions = [
            'modelClass' => false,
            'fields' => [
                'key' => [],
                'className' => [],
                'initialClassName' => [],
                'originalClassName' => [],
                'debugkitLog' => [],
            ]
        ];
        $dt = $this->DataTable->create($dtOptions, $transports);
        echo $dt->render();
        ?>
    </div>
</div>
