<?php namespace App\Controllers\Graph;

class Home extends \App\Controllers\BaseController {
	
protected function getSegments() {
	$segments = $this->request->uri->getSegments();
	/*
	2: method
	3: dt_start (optional)
	4: dt_end (optional)
	end: display (optional) 
	*/
		
	$value = $segments[3] ?? '' ;
	$dt_start = $this->get_datetime($value, 'value');
	
	$value = $segments[4] ?? '' ;
	$dt_end = $this->get_datetime($value, 'value');
	
	$display = end($segments) ?? null ;
			
	if($dt_end && $dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}
	
	$segments = array_slice($segments, 0, 3);
	if($dt_start) $segments['dt_start'] = $dt_start;
	if($dt_end) $segments['dt_end'] = $dt_end;
	$segments['display'] = $display;
	
	#d($segments); die;
	
	return $segments;	
}

// check for cached image
protected function check_cache($segments) {
	$cache_data = [];
	
	// get cache name
	$arr = ['dt_start', 'dt_end'];
	foreach($arr as $key) {
		$val = $segments[$key] ?? null;
		$segments[$key] = $val ? $val->format('YmdHi') : '' ;
	}
	$cache_data['name'] = implode('_', $segments);
	
	$time = $this->request->getGet('t') ?? 900; // 15 minutes cache
	if($time) $cache_data['time'] = $time;

	// don't cache
	if(ENVIRONMENT!=='production') return ''; 
	
	$cache = \Config\Services::cache();
	$response = $cache->get($cache_data['name']);
	# d($cache_data); echo $response ? 'cached' : 'not cached'; die;
	if(!$response) return $cache_data; // nothing in cache

	// send cached image
	log_message('debug', "cache: retrieved {$cache_data['name']}");
	header('content-type: image/png');
	echo $response;
	die;
}

public function getIndex() {
	\App\ThirdParty\jpgraph::blank();
}

}
