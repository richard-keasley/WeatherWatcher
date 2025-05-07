<section class="flex">
<div>
<h4>credits</h4>
<ul>
<?php
$ul = [
	['https://realfavicongenerator.net/', 'Favicon Generator'],
	['https://moonphases.co.uk/', 'moon images'],
	['https://www.astropixels.com/', 'Solstice info'],
	['https://jpgraph.net/', 'JP graph'],
	['https://github.com/Moskito89/php-moon-phase', 'Moon phase calculations'],
	['https://github.com/richard-keasley/suninfo', 'Sun calculations'],
];
foreach($ul as $li) {
	printf('<li>%s</li>', \anchor($li[0], $li[1]));
}
?></ul>
</div>

<?php if(ENVIRONMENT=='development') { ?>
<section>
<h4>Third-party classes</h4>
<?php
$tbody = [['class', 'version', 'date']];
$files = new \CodeIgniter\Files\FileCollection([]);
$files->addDirectory(APPPATH . 'ThirdParty');
$files->removePattern('#\.git#');
foreach($files as $file) {
	$classname = $file->getBasename('.php');
	$fqn = "\App\ThirdParty\\{$classname}"; 
	$class = new $fqn;
	$tbody[] = [
		$classname, 
		$class::version,
		$class::date
	];	
}
$table = \App\Views\Htm\table::load('bordered');
echo $table->generate($tbody);
?>
</section>
<?php } ?>

</section>