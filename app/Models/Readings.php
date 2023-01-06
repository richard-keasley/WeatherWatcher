<?php namespace App\Models;

use CodeIgniter\Model;

class Readings extends Model {

protected $table = 'readings';
protected $returnType = \App\Entities\Reading::class;
protected $allowedFields = ['datetime', 'server', 'readings', 'inputs'];

function add_reading($data) {
	$data['server'] = $_SERVER;
	$reading = new \App\Entities\Reading($data);
	$this->insert($reading);	
	# d($this->db->getLastQuery());
}

function get_current() {
	return $this->orderBy('datetime', 'desc')->first();
}

function get_first() {
	return $this->orderBy('datetime', 'asc')->first();
}

}
