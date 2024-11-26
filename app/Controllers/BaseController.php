<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller {
/**
 * Instance of the main Request object.
 *
 * @var CLIRequest|IncomingRequest
 */
protected $request;

/**
 * An array of helpers to be loaded automatically upon
 * class instantiation. These helpers will be available
 * to all other controllers that extend BaseController.
 *
 * @var array
 */
protected $helpers = ['form', 'inflector', 'html'];

protected $data = [
	'title' => 'base-camp weather',
	'message' => ''
];

/**
 * Constructor.
 */
public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
	// Do Not Edit This Line
	parent::initController($request, $response, $logger);
	
	// Preload any models, libraries, etc, here.
	$this->data['api'] = new \App\Libraries\Apis\Ecowitt;
	$this->data['listener'] = new \App\Libraries\Listeners\Ecowitt;
	$this->data['readings'] = new \App\Models\Readings;

	$this->auth();
		
	// garbage collection
	$gc_file = WRITEPATH . 'gc_last';
	$gc_period = config('App')->gc_period;
	if(file_exists($gc_file)) {
		if(filemtime($gc_file) < time() - $gc_period) {
			$this->garbage_collection();
			touch($gc_file);
		}
	}
	else touch($gc_file);
}

protected function auth() {	
	$segments = $this->request->uri->getSegments();
	$zone = $segments[0] ?? '' ;
	
	// allowed for everyone
	$allowed = ['upload', 'auth'];
	if(in_array($zone, $allowed)) return;

	// logged in user
	if(session('usr')===config('App')->usr) return;
	
	// image 
	switch($zone) {
		case 'graph':
		\App\ThirdParty\jpgraph::blank();
		break;
		
		default:
		$url = base_url('auth');
		header("Location: {$url}");
	}
	die;
}

protected function garbage_collection() {
	// delete temp files
	$pattern = WRITEPATH . '*';
	$files = new \CodeIgniter\Files\FileCollection();
	foreach(glob($pattern, GLOB_ONLYDIR) as $directory) {
		$files->addDirectory($directory);
	}
	$del_before = time() - config('cache')->ttl;
	$count = 0;
	foreach($files as $file) {
		$basename = $file->getBaseName();
		if(strpos($basename, '.')===0) continue; // hidden
		if(strpos($basename, 'index.')===0) continue; // index
		if($file->getMtime() > $del_before) continue; // too young
		unlink($file->getRealPath());	
		$count++;
	}
	# d('garbage_collection', $del_before, $count);
	# d($files);	
	
	// clear old entries from readings
	$delete_readings = config('App')->delete_readings;
	if($delete_readings) {
		$datetime = new \DateTime();
		$interval = new \DateInterval($delete_readings);
		$where = $datetime->sub($interval)->format('Y-m-d H:i:s');
		$this->data['readings']->where('datetime <', $where)->delete();
		# d($where);
	}
	
	// update dailies
	$cfg_daily = config('App')->update_daily;
	if($cfg_daily) {
		$dt_interval = new \DateInterval('P1D');
		$dailies = new \App\Models\Dailies;
		$dt_request = $dailies->dt_last()->add($dt_interval);
		$dt_last = new \DateTime(); // now
		$dt_last->setTime(0, 0); // midnight today
		while($dt_request<$dt_last) { // before today
			$daily = $this->data[$cfg_daily]->get_daily($dt_request);
			# d($dt_request, $daily);
			if($daily->count) $dailies->insert($daily);
			$dt_request->add($dt_interval);
		}
	}
	return $count;
}
	
protected function get_datetime($fldname, $method='get') {
	$string = match($method) {
		'get' => $this->request->getGet($fldname),
		'post' => $this->request->getPost($fldname),
		'value' => $fldname,
		default => $this->request->getVar($fldname)
	};
	if(!$string) return null;
	
	try {
		$datetime = new \datetime($string);
		return $datetime;
	}
	catch(\Exception $e) {
		return null;
	}
}

}
