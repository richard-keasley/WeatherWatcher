<?php namespace App\Controllers\Test;

class Graph extends Home {

public function getIndex() {
	

	return view('test/graph', $this->data);
}


}
