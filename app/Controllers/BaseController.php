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
	'message' => '',
	'serviceworker' => true,
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
