<?php $this->extend('template');

$this->section('header'); ?>
<h1>Data</h1>
<?php $this->endSection();

$this->section('main'); 
$tbody = []; $thead = [];
foreach($data as $dataname=>$dataset) {
	$thead[] = $dataname;
	foreach($dataset as $rowkey=>$value) {
		$tbody[$rowkey][$dataname] = $value;
	}
}

$table = \App\Views\Htm\table::load();
$table->setHeading($thead);
echo $table->generate($tbody);

$this->endSection();