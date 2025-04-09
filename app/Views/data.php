<?php $this->extend('template');

$this->section('header'); ?>
<h1>Data</h1>
<?php $this->endSection();

$this->section('main'); 
$tbody = [];
$dt = new \DateTime;
foreach($data as $dataname=>$dataset) {
	$thead[] = $dataname;
	foreach($dataset as $rowkey=>$value) {
		if($dataname=='datetime') {
			$value = $dt->setTimestamp($value)->format('Y-m-d H:i');
		}
		$tbody[$rowkey][$dataname] = $value;
	}
}

$table = \App\Views\Htm\table::load();
$table->setHeading($thead);
echo $table->generate($tbody);

# d($data);
		

$this->endSection();