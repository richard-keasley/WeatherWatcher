<?php namespace App\Controllers\Graph;

class Home extends \App\Controllers\BaseController {
	
protected function getSegments($options) {
	$segments = $this->request->getUri()->getSegments();
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
				
	if($dt_end && $dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}
	
	// valid display
	$displays = ['line', 'bar', 'table'];
	$display = end($segments) ?? '' ;
	if(!in_array($display, $displays)) $display = $options['display'] ?? '';
	if(!in_array($display, $displays)) $display = $displays[0];
		
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
		
	$cache_time = $this->request->getGet('t') ?? 900; // 15 minutes cache
	if($cache_time) $cache_data['time'] = $cache_time;
	
	// disable cache
	# return $cache_data; 
		
	$cache_opts = [
		'max-age'  => $cache_time,
		's-maxage' => $cache_time,
		'etag'     => $cache_data['name'],
	];
	
	// image cached by client?
	$request_tag = $this->request->getHeaderLine('if-none-match');
	if($request_tag===$cache_data['name']) {
		$this->response
			->setBody('')
			->setCache($cache_opts)
			->setHeader('content-type', 'image/png')
			->setHeader('X-Robots-Tag', ['noindex', 'nofollow'])
			->setStatusCode(304)
			->send();
		die;	
	}
	 
	// image cached by server?
	$cache = \Config\Services::cache();
	$response = $cache->get($cache_data['name']);
	# print_r($cache_data); echo $response ? 'cached' : 'not cached'; die;
	if($response) {
		// send cached image from server
		log_message('debug', "cache: retrieved {$cache_data['name']}");
		$this->response
			->setBody($response)
			->setCache($cache_opts)
			->setHeader('content-type', 'image/png')
			->setHeader('X-Robots-Tag', ['noindex', 'nofollow'])
			->send();
		die;
	}	
	
	// nothing in cache
	return $cache_data; 
}

public function getIndex() {
	\App\ThirdParty\jpgraph::blank();
}

}
