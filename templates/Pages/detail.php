<?php
$this->assign('title', 'Service detail');
$this->start('nav');

?>
<li><a href="<?= $this->Url->build('/') ?>"><span>Home</span></a></li>
<li><a href="<?= $this->Url->build('/edit/0') ?>"><span>Add a service</span></a></li>
<li><a href="<?= $this->Url->build('/mrr') ?>"><span>Monthly sum payment</span></a></li>
<?php $this->end()?>

<div class="content">
    <div class="header">
        <h1 >
            Details of <?= h($service['name']) ?>
        </h1>
    </div>
    <hr>
    <div class="form-horizontal">
    <?php while (($serviceVal = current($service)) !== False): ?>
    <?php if (in_array(key($service), ['billing_period_id', 'currency_id'])):
       next($service);
        continue;
        endif; ?>

    <div class="form-group">
        <label class="col-lg-2 control-label"><?php echo ucfirst(trim(str_replace("_", ' ', key($service)))) ?> : </label>
        <div class="col-lg-10 detail-data"><?php echo h($serviceVal) ?></div>
        <?php next($service) ?>
    </div>
    <?php endwhile; ?>
    </div>
</div>
