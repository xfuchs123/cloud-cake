<?php
$this->assign('title', 'Monthly sum payment - CRUD service management');
$this->start('nav');

?>
<li ><a href="<?= $this->Url->build('/') ?>"><span>Home</span></a></li>
<li><a href="<?= $this->Url->build('/edit/0') ?>"><span>Add a service</span></a></li>
<li class="active"><a href="<?= $this->Url->build('/mrr') ?>"><span>Monthly sum payment</span></a></li>
<?php $this->end()?>
<?= $this->Html->script('mrr', ['block' => true]) ?>
<div class="content">
    <div class="header">
        <h1 >Monthly sum payment

        </h1>
    </div>
        <hr>
        <?= $this->Form->create($mrr) ?>
        <?= $this->Form->control('date')?>
        <?php if (!empty($errors)): ?>
        <?php foreach ($errors['date'] as $error): ?>
    <div class="error-input-text"><?= $error ?></div>
    <?php endforeach; ?>
      <?php  endif; ?>

        <div class="submit-btn"><?= $this->Form->submit('Submit this date') ?></div>




        <?= $this->Form->end() ?>
    <hr>
    <?php if (isset($date)): ?>
    <div class="row">
    <div class="col-lg-12 info-text">

        For the given date <?= $date ?> there is monthly payment of <?= $bill ?> Euro for all services .
    </div>
    </div>
    <?php endif ?>
</div>
