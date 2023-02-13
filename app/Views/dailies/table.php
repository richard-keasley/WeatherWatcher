<?php 
$tbody = []; 
foreach($dailies as $rowkey=>$daily) {
	$row = $daily->table_cells();
	if(!$rowkey) {
		$tbody[] = $daily->table_head();
	}
	$tbody[] = $row;
}
if($tbody) {
	$table = \App\Views\Htm\table::load();
	echo $table->generate($tbody);
}
else {
	echo new \App\Views\Htm\alert('No data');

}