<?php namespace App\Controllers\Test;

class Readings extends Home {
	
public function postIndex($date='###') {
	try {
		$datetime = new \datetime($date);
	}
	catch(\exception $ex) {
		$datetime = null;
	}
	if(!$datetime) return $this->getIndex($date);
	
	$update = $this->request->getPost('update');
	if($update=='readings') {
		$model = new \App\Models\Readings;
		$daily = $model->get_daily($datetime);
		if($daily->count) {
			$dailies = new \App\Models\Dailies;
			$dailies->save($daily);
		}
	}
	return $this->getIndex($date);
}

public function getIndex($date='') {
	try {
		$datetime = new \datetime($date);
	}
	catch(\exception $ex) {
		$datetime = new \datetime();
	}
	$this->data['datetime'] = $datetime;

	$model = new \App\Models\Readings;
	$this->data['readings'] = $model->get_daily($datetime);
	
	$model = new \App\Models\Dailies;
	$this->data['dailies'] = $model->find($datetime->format('Y-m-d'));

	return view('test/readings', $this->data);
}

}
