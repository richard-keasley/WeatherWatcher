<?php namespace App\Controllers\Test;

class Home extends \App\Controllers\BaseController {

function __construct() {
	if(ENVIRONMENT!='development') throw new \RuntimeException('Service closed', 404);
}

public function getIndex() {
	$path = realpath(APPPATH . '/Controllers/Test');
	$files = new \CodeIgniter\Files\FileCollection([$path]);
	$files->removeFile(__FILE__);
		
	$base_url = 'test';
	$links = [anchor($base_url, $base_url)];
	foreach($files as $file) {
		$label = strtolower($file->getBasename('.php'));
		$links[] = anchor("{$base_url}/{$label}", $label);
	}
	
	$this->data['links'] = $links;
	return view('test/index', $this->data);
}

}
