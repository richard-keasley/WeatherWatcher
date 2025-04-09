<?php $this->extend('template');

$this->section('header'); ?>
<h1>Login</h1>
<?php $this->endSection();

$this->section('top');
$this->endSection();

$this->section('main');
$attrs = ['class' => "border"]; 
echo form_open('auth', $attrs); ?>
<p class="flex"><label>User</label><input type="text" name="usr" value="<?php echo $usr;?>"></p>
<p class="flex"><label>Password</label><input type="password" name="pwd" value="<?php echo $pwd;?>"></p>
<p class="flex"><button type="submit">Login</button></p>
<?php echo form_close();
$this->endSection();

$this->section('bottom'); ?>
<?php $this->endSection();
