<?php namespace App\Controllers\Graph;

class Home extends \App\Controllers\BaseController {
	
protected function getSegments() {
	$segments = $this->request->uri->getSegments();
	/*
	2: method
	3: dt_start (optional)
	4: dt_end (optionsl)
	*/
		
	$value = $segments[3] ?? '' ;
	$dt_start = $this->get_datetime($value, 'value');
	
	$value = $segments[4] ?? '' ;
	$dt_end = $this->get_datetime($value, 'value');
	# d($dt_start, $dt_end);
		
	if($dt_end && $dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}
	
	$segments = array_slice($segments, 0, 3);
	if($dt_start) $segments['dt_start'] = $dt_start;
	if($dt_end) $segments['dt_end'] = $dt_end;
	
	# d($segments);
	
	return $segments;	
}

// check for cached image
protected function check_cache($segments) {
	$cache_data = [];
	
	// get cache name
	$arr = ['dt_start', 'dt_end'];
	foreach($arr as $key) {
		if(isset($segments[$key])) {
			$segments[$key] = $segments[$key]->format('YmdHi');
		}
	}
	$cache_data['name'] = implode('_', $segments);
	
	$time = $this->request->getGet('t') ?? 600; // 10 minutes cache
	if($time) $cache_data['time'] = $time;

	// don't cache
	# if(ENVIRONMENT!=='production') return ''; 
	
	$cache = \Config\Services::cache();
	$response = $cache->get($cache_data['name']);
	# d($cache_data); echo $response ? 'cached' : 'not cached'; die;
	if(!$response) return $cache_data; // nothing in cache
	
	// send cached image
	header('content-type: image/png');
	echo $response;
	die;
}

public function getIndex() {
	\App\ThirdParty\jpgraph::blank();
}

}
