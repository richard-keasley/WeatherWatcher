<?php

namespace App\Filters;

class Garbage implements \CodeIgniter\Filters\FilterInterface {
	
public function before($request, $arguments = null) {
	
}

public function after($request, $response, $arguments = null) {
	$gc_file = WRITEPATH . 'gc_last';
	$gc_period = config('App')->gc_period;
	if(file_exists($gc_file)) {
		if(filemtime($gc_file) < time() - $gc_period) {
			$this->update();
			$this->execute();
			touch($gc_file);
		}
	}
	else touch($gc_file);
}

private function update() {
	// not garbage collection!

	// update dailies
	$config = config('App')->update_daily;
	$reader = match($config) {
		'readings' => new \App\Models\Readings,
		'ecowitt' => new \App\Libraries\Apis\Ecowitt,
		default => null
	};
	
	if($reader) {
		$dt_interval = new \DateInterval('P1D');
		$dailies = new \App\Models\Dailies;
		// date of next daily to be inserted
		$dt_request = $dailies->dt_last()->add($dt_interval);
		$dt_last = new \DateTime(); // now
		$dt_last->setTime(0, 0); // midnight today
		
		while($dt_request<$dt_last) { // before today
			$daily = $reader->get_daily($dt_request);
			# d($dt_request, $daily);
			if($daily->count) $dailies->insert($daily);
			$dt_request->add($dt_interval);
		}
	}
}

private function execute() {
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
	# d($files); return;	
	
	// clear old entries from readings
	$delete_readings = config('App')->delete_readings;
	if($delete_readings) {
		$datetime = new \DateTime();
		$interval = new \DateInterval($delete_readings);
		$readings = new \App\Models\Readings;
		$where = $datetime->sub($interval)->format('Y-m-d H:i:s');
		$readings->where('datetime <', $where)->delete();
		# d($where);
	}
}

}
