<?php

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'cake', 'styles']) ?>
    <?= $this->Html->css("https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css") ?>
    <?= $this->Html->css("https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css") ?>
    <?= $this->Html->charset() ?>
    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js') ?>
    <?= $this->Html->script('https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js') ?>
    <?= $this->Html->script('https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="navbar navbar-inverse ">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="<?= $this->Url->build('/') ?>" class="navbar-brand"><span>CRUD Services management</span></a>
            </div>
        <ul class="nav navbar-nav">
            <?= $this->fetch('nav') ?>
        </ul>

        </div>
    </nav>
    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer class="text-center">
        &copy;&nbsp;Copyright 2021 Peter Fuchs
    </footer>
</body>
</html>
