<?php namespace App\Controllers\Test;

class Garbage extends Home {

public function getIndex() {
	# $this->cachePage(60);
	
	
	$pattern = WRITEPATH . '*';
	$files = new \CodeIgniter\Files\FileCollection();
	foreach(glob($pattern, GLOB_ONLYDIR) as $directory) {
		$files->addDirectory($directory);
	}
	$files->removePattern('#index\.#');
	
	$this->data['files'] = $files;
	
	
	return view('test/garbage', $this->data);
}

}
