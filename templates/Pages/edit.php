<?php
if ($service->isNew()):
    $this->assign('title', 'New service - CRUD service management');
else:
    $this->assign('title', 'Edit a service - CRUD service management');
endif;

$this->start('nav');

?>
<li ><a href="<?= $this->Url->build('/') ?>"><span>Home</span></a></li>
<li class="active"><a href="<?= $this->Url->build('/edit/0') ?>"><span>Add a service</span></a></li>
<li><a href="<?= $this->Url->build('/mrr') ?>"><span>Monthly sum payment</span></a></li>
<?php $this->end()?>
<?= $this->Html->script('edit', ['block' => true]) ?>

<div class="content">
    <div class="header">
        <h1 >
            <?php if ($service->isNew()): ?>
            New Service
            <?php else: ?>
            Edit óf <?= h($service->name) ?>
            <?php endif; ?>
        </h1>
    </div>
    <hr>

    <?= $this->Form->create($service, ['type' => 'post']) ?>

    <?= $this->Form->control('name') ?>
    <?= $this->Form->control('unit_cost') ?>
    <?= $this->Form->control('valid_from', ['type' => 'text']) ?>
    <?= $this->Form->control('valid_to', ['type' => 'text']) ?>
    <?= $this->Form->control('currency_id') ?>
    <?= $this->Form->control('billing_period_id', ['options' => $billingPeriodOpts]) ?>
    <?= $this->Form->control('notes') ?>
    <hr>
    <?= $this->Form->submit('Save') ?>
    <?= $this->Form->end() ?>
</div>
