<?php namespace App\Controllers\Test;

class Import extends Home {

private function init() {
	$path = '/home/basecamp/public_html/weather/weewx/data';
	$collection = new \CodeIgniter\Files\FileCollection([]);
	$collection->addDirectory($path);
	$collection->retainPattern('????-??.csv');
	$this->data['datafiles'] = $collection;
	$this->data['datarows'] = [];
}

public function getIndex() {
	$this->init();
	return view('test/import', $this->data);
}

public function postIndex() {
	$this->init();
	
	$map = [
	'Date' => 'date',
	'TempHi' => 'temperature_max',
	'TempAv' => 'temperature_avg',
	'TempLo' => 'temperature_min',
	'WindHi' => 'wind_max',
	'WindAv' => 'wind_avg',
	'Rain' => 'rain_max'
	];
	
	foreach($this->data['datafiles'] as $file) {
		$csvkeys = []; 
		$datarow = [];

		$csv = $file->openFile();
		$csv->setFlags(\SplFileObject::READ_CSV);
		foreach($csv as $rowkey=>$csvrow) {
			if($rowkey) {
				$include = !empty($csvrow[1]);
				if($include) {
					foreach($csvkeys as $key=>$fldname) {
						$dest = $map[$fldname] ?? null;
						if($dest) {
							$datarow[$dest] = $csvrow[$key] ?? '' ;
						}
					}
					$this->data['datarows'][] = $datarow;
				}
			}
			else { //  header row
				$csvkeys = $csvrow;
			}
		}
		# if(count($this->data['datarows']) > 20) break;
	}
	
	if($this->request->getpost('commit')) {
		$this->commit();
	}

	return view('test/import', $this->data);
}

private function commit() {
	$model = new \App\Models\Dailies;
	foreach($this->data['datarows'] as $datarow) {
		$daily = new \App\Entities\Daily($datarow);
		$id = $model->insert($daily);
	}
}

}
