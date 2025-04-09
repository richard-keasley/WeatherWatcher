<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Weather in Portslade village">
<meta name="robots" content="noindex, nofollow"> 
<meta name="theme-color" content="#B22900">
<meta name="msapplication-TileColor" content="#FAFAFA">
<meta name="msapplication-config" content="<?php echo base_url('app/browserconfig.xml');?>">
<?php
$links = [
[
	'rel' => "stylesheet",
	'href' => "app/style.css",
	'type' => "text/css"
],
[
	'rel' => "apple-touch-icon",
	'sizes' => "180x180",
	'href' => "app/apple-touch-icon.png"
],
[
	'rel' => "icon",
	'type' => "image/png",
	'sizes' => "32x32",
	'href' => "app/favicon-32x32.png"
],
[
	'rel' => "icon",
	'type' => "image/png",
	'sizes' => "16x16",
	'href' => "app/favicon-16x16.png"
],
[
	'rel' => "manifest",
	'href' => "app/site.webmanifest",
],
[
	'rel' => "mask-icon",
	'href' => "app/safari-pinned-tab.svg",
	'color' => "#b22900"
],
[
	'rel' => "shortcut icon",
	'href' => "favicon.ico",
	'type' => "image/x-icon"
]
];
foreach($links as $link) echo link_tag($link);
?>
<title><?php echo $title;?></title>
<style>
.table tfoot td {
	background: var(--theme-bg-light);
}

</style>
</head>
<body>
<?php
$attrs = ['class' => "flex"];
if(ENVIRONMENT!='production') { 
	$attrs['style'] = 'background: repeating-linear-gradient(45deg, #B22900, #d7673b 3em, #B22900 6em);';
}
?>
<header <?php echo stringify_attributes($attrs);?>>
<div><?php
$img = [
	'src' => 'app/header.png',
	'style' => "height:3em;"
];
echo anchor('/', img($img));
?></div>
<div><?php echo $this->renderSection('header');?></div>
</header>

<?php
$attrs = [];
if(ENVIRONMENT!='production') { 
	$attrs['style'] = 'background: repeating-linear-gradient(135deg, #fafafa, #fbf6ef 3em, #fafafa 6em);';
}
?>
<main <?php echo stringify_attributes($attrs);?>>
<?php if(!empty($this->sections['top'])) { ?>
	<section class="top">
	<?php echo $this->renderSection('top');?>
	</section>
<?php } ?>
<section class="main">
<?php echo $this->renderSection('main');?>
</section>
<?php if(!empty($this->sections['bottom'])) { ?>
	<section class="bottom">
	<?php echo $this->renderSection('bottom');?>
	</section>
<?php } ?>
</main>

<?php
$attrs = [];
if(ENVIRONMENT!='production') { 
	$attrs['style'] = 'background: repeating-linear-gradient(45deg, #B22900, #d7673b 3em, #B22900 6em);';
}
?>
<footer <?php echo stringify_attributes($attrs);?>>
<?php echo $this->renderSection('footer'); ?>
<ul class="nav"><?php
$img = [
	'src' => 'app/header.png',
	'style' => "height:2em;",
	'title' => "Start page"
];

$links = [
	['/', img($img)],
	['readings', 'readings'],
	['dailies', 'dailies'],
	['about', 'about']
];
if(ENVIRONMENT!='production') $links[] =  ['test', 'test'];

foreach($links as $link) {
	printf('<li>%s</li>', anchor($link[0], $link[1]));
}
?></ul>

<?php if(ENVIRONMENT!='production') { ?>
<div class="flex">
<p>Page rendered in {elapsed_time} seconds</p>
<p>Environment: <?= ENVIRONMENT ?></p>
</div>
<?php } ?>

</footer>
<?php echo $this->renderSection('bottom');?>
</body>
</html>
