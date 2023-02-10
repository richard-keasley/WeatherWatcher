<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Weather in Portslade village">
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
</head>
<body>
<header class="flex">
<div><?php
$img = [
	'src' => 'app/header.png',
	'style' => "height:3em;"
];
echo anchor('/', img($img));
?></div>
<div><?php $this->renderSection('header');?></div>
</header>
<main>
<?php if(!empty($this->sections['top'])) { ?>
	<section class="top">
	<?php $this->renderSection('top');?>
	</section>
<?php } ?>
<section class="main">
<?php $this->renderSection('main');?>
</section>
<?php if(!empty($this->sections['bottom'])) { ?>
	<section class="bottom">
	<?php $this->renderSection('bottom');?>
	</section>
<?php } ?>
</main>
<footer>
<?php $this->renderSection('footer'); ?>
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
<?php $this->renderSection('bottom');?>
</body>
</html>
