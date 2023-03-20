<?php $this->extend('template');

$this->section('header'); ?>
<h1>Testing</h1>
<?php $this->endSection();

$this->section('main'); ?>
<p><strong>Warning:</strong> Exit development mode once you have finished testing.</p> 

<ul class="list-unstyled"><?php
foreach($links as $link) echo "<li>{$link}</li>";
?></ul>

<?php $this->endSection();

$this->section('bottom');
echo phpinfo();
$this->endSection();