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
	
	// get cache name
	$arr = ['dt_start', 'dt_end'];
	foreach($arr as $key) {
		$val = $segments[$key] ?? null;
		$segments[$key] = $val ? $val->format('YmdHi') : '' ;
	}
		
	$cache_time = $this->request->getGet('t') ?? 900; // 15 minutes cache
	
	$cache_data = [
		'name' => implode('_', $segments),
		'time' => (int) $cache_time,
		'status' => 0,
	];
	
	// disable cache
	# return $cache_data; 
		
	// image cached by client?
	$request_tag = $this->request->getHeaderLine('if-none-match');
	if($request_tag===$cache_data['name']) {
		$cache_data['status'] = 304;
		$this->response->setBody('');
		return $cache_data;
	}
	 
	// image cached by server?
	$cache = \Config\Services::cache();
	$imgdata = $cache->get($cache_data['name']);
	if($imgdata) {
		$cache_data['status'] = 200;
		$this->response->setBody($imgdata);
		return $cache_data;
	}	
	
	// nothing in cache
	return $cache_data; 
}

// send cached image back to browser
protected function send_cached($cache_data) {
	$cache_opts = [
		'max-age'  => $cache_data['time'],
		's-maxage' => $cache_data['time'],
		'etag'     => $cache_data['name'],
	];
	
	if(0) {
		echo '<pre>'; 
		print_r($cache_data);
		print_r($cache_opts);
		# var_dump($this->response);
		return;
	}
	log_message('debug', "cache sent {$cache_data['name']} / {$cache_data['status']}");
	
	$this->response
		->setCache($cache_opts)
		->setStatusCode($cache_data['status'])
		# ->setBody($cache_data['body'])
		->setHeader('content-type', 'image/png');
	return $this->response;
}

public function getIndex() {
	return \App\ThirdParty\jpgraph::blank();
}

}
