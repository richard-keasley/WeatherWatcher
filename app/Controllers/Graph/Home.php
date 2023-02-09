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
		
	if($dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}
	
	$segments = array_slice($segments, 0, 3);
	if($dt_start) $segments['dt_start'] = $dt_start;
	if($dt_end) $segments['dt_end'] = $dt_end;
	return $segments;	
}

protected function check_cache($segments) {
	// don't cache
	if($this->request->getGet('v')) return '';
	
	// check for cached image
	$arr = ['dt_start', 'dt_end'];
	foreach($arr as $key) {
		if(isset($segments[$key])) {
			$segments[$key] = $segments[$key]->format('YmdHi');
		}
	}
	$cache_name = implode('_', $segments);
	
	$cache = \Config\Services::cache();
	$response = $cache->get($cache_name);

	# d($cache_name); echo $response ? 'cached' : 'not cached'; die;
	if(!$response) return $cache_name; // nothing in cache
	if(ENVIRONMENT!=='production') return $cache_name; // don't cache development
	
	// send cached image
	header('content-type: image/png');
	echo $response;
	die;
}

public function getIndex() {
	\App\ThirdParty\jpgraph::blank();
}

}
