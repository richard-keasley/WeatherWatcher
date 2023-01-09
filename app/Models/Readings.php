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

function get_daily($date) {
	$datetime = new \DateTime($date);
	$daily = [
		'date' => $datetime->format('Y-m-d'),
	];
	$start = $daily['date'];
	$interval = new \DateInterval("P1D");
	$datetime->add($interval);
	$end = $datetime->format('Y-m-d');
	$res = $this->where('datetime >=', $start)
		->where('datetime <', $end)
		->findAll();
	
	$day_data = [];
	foreach($res as $row) {
		$row = array_flatten_with_dots($row->readings);
		$day_data[] = $row;
	}
	$daily['count'] = count($day_data);
	if(!$daily['count']) return new \App\Entities\Daily($daily);
	
	$map = [
	'temperature' => ['temperature.out', ['min', 'avg', 'max']],
	'humidity' => ['humidity.out', ['min', 'avg', 'max']],
	'rain' => ['rain.day', ['max']],
	'solar' => ['solar.radiation', ['avg', 'max']],
	'uvi' => ['solar.uv', ['avg', 'max']],
	'wind' => ['wind.speed', ['avg', 'max']],
	];
	foreach($map as $dest=>$info) {
		$column = array_column($day_data, $info[0]);
		// not needed once old dailies are deleted
		if(!$column) $column = array_column($day_data, $info[0] . '.value');
		if($column) {
			$sum = array_sum($column);
			foreach($info[1] as $param) {
				$daily["{$dest}_{$param}"] = match($param) {
					'min' => min($column),
					'avg' => $sum / $daily['count'],
					'max' => max($column)
				};
			}
		}
	}
	return new \App\Entities\Daily($daily);
}

}
