<?php namespace App\Models;

use CodeIgniter\Model;

class Readings extends Model {

protected $table = 'readings';
protected $returnType = \App\Entities\Reading::class;
protected $allowedFields = ['datetime', 'server', 'readings', 'inputs'];

function add_reading($data) {
	$data['server'] = $_SERVER;
	$reading = new \App\Entities\Reading($data);
	$this->insert($reading);	
	# d($this->db->getLastQuery());
}

function get_current() {
	return $this->orderBy('datetime', 'desc')->first();
}

function get_first() {
	return $this->orderBy('datetime', 'asc')->first();
}

function get_daily($datetime) {
	$date = $datetime->format('Y-m-d');
	$dt_end = new \DateTime($date);
	$interval = new \DateInterval("P1D");
	$end = $dt_end->add($interval)->format('Y-m-d');
		
	$res = $this->where('datetime >=', $date)
		->where('datetime <', $end)
		->findAll();
	
	$day_data = [];
	foreach($res as $row) {
		$row = array_flatten_with_dots($row->readings);
		$day_data[] = $row;
	}
	
	$map = [
	'temperature' => ['temperature.out', ['min', 'avg', 'max']],
	'humidity' => ['humidity.out', ['min', 'avg', 'max']],
	'rain' => ['rain.day', ['max']],
	'solar' => ['solar.radiation', ['avg', 'max']],
	'uvi' => ['solar.uv', ['avg', 'max']],
	'wind' => ['wind.speed', ['avg', 'max']],
	];
		
	$daily = [
		'date' => $date,
		'count' => count($day_data)
	];
	
	if($daily['count']) {
		foreach($map as $dest=>$info) {
			// get this dataset
			$arr = array_column($day_data, $info[0]);	
			if($arr) {
				// discard null readings
				$data = [];
				foreach($arr as $val) {
					if(!is_null($val)) $data[] = $val;
				}
				// aggregate values			
				foreach($info[1] as $param) {
					$daily["{$dest}_{$param}"] = match($param) {
						'min' => min($data),
						'avg' => array_sum($data) / count($data),
						'max' => max($data)
					};
				}
			}
		}
	}
	return new \App\Entities\Daily($daily);
}

}
