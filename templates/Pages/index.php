<?php
$this->assign('title', 'Service list - CRUD service management');
$this->start('nav');

?>
<li class="active"><a href="<?= $this->Url->build('/') ?>"><span>Home</span></a></li>
<li><a href="<?= $this->Url->build('/edit/0') ?>"><span>Add a service</span></a></li>
<li><a href="<?= $this->Url->build('/mrr') ?>"><span>Monthly sum payment</span></a></li>
<?php $this->end()?>
<?= $this->Html->script('index', ['block' => true]) ?>
<div class="content">
    <div class="header">
        <h1 >Service list
            <?= $this->Html->image("add_circle_outline_48.png", [
                'alt' => 'add_Service',
                'url' => ['controller' => 'Pages' , 'action' => 'edit', 0],
                'title' => 'Add a service'
            ]) ?>
        </h1>
    </div>
    <hr>

    <div class="table-header float-right">Showing services &nbsp;<?= $this->Paginator->counter('range') ?> &nbsp;</div>

    <table class="table-striped table-bordered table table-hover">
        <tr>
            <th><?= $this->Paginator->sort('Services.name', 'Service') ?></th>
            <th><?= $this->Paginator->sort('Services.unit_cost', 'Unit cost') ?></th>
            <th><?= $this->Paginator->sort('BillingPeriods.type', 'Billing period') ?></th>
            <th><?= $this->Paginator->sort('Services.valid_from', 'Valid from') ?></th>
            <th><?= $this->Paginator->sort('Services.valid_to', 'Valid to') ?></th>
            <th><?= $this->Paginator->sort('Currencies.name', "Currency") ?></th>
            <th>Action</th>
        </tr>
        <?php foreach ($services as $service): ?>
        <tr>
            <td><?= h($service->name) ?></td>
            <td><?= $service->unit_cost ?></td>
            <td><?= h($service->billing_period->type) ?></td>
            <td><?= h($service->valid_from) ?></td>
            <td><?= h($service->valid_to) ?></td>
            <td><?= h($service->currency_name) ?></td>
            <td>
                <a href="<?= $this->Url->build('/detail/'.$service->id) ?>" data-toggle="tooltip" title="Service detail"><span class="glyphicon glyphicon-eye-open"></span></a> &nbsp;
                <a href="<?= $this->Url->build('/edit/'.$service->id) ?>" data-toggle="tooltip" title="Edit service"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;
                <a href="<?= $this->Url->build('/delete/'.$service->id) ?>" data-toggle="tooltip" title="Delete service"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <ul class="pagination">
    <?php
    echo $this->Paginator->prev();
    echo $this->Paginator->numbers();
    echo $this->Paginator->next();
    ?>
    </ul>

</div>
