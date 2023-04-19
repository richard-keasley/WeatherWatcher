<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Daily extends Entity {

function get_date($format=null) {
	// date is saved as UTC/GMT string
	// convert to local time
	$tz_utc = new \DateTimeZone('UTC');
	$tz_local = new \DateTimeZone(app_timezone());
	$datetime = new \DateTime($this->date, $tz_utc);
	$datetime->setTimezone($tz_local);
	return $format ? $datetime->format($format) : $datetime ;
}

}