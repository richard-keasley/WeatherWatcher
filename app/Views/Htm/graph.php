<?php namespace App\Views\Htm;

class graph implements \stringable {

public $dataname = null;
public $start = null;
public $end = null;
public $template = 
'<div class="graph"><figure>
<a href="{src}"><img src="{src}"></a>
<figcaption>{caption}</figcaption>
</figure></div>';

function __construct($controller='dailies', $dataname='temperature', $start=null, $end=null) {
	$this->controller = $controller;
	$this->dataname = $dataname;
	$this->start = $start;
	$this->end = $end;
}

function __toString() {
	$controller = $this->controller;
	if(!$controller) return '';
	$dataname = $this->dataname;
	if(!$dataname) return '';
	$start = $this->start;
	if(!$start) return '';
	
	$end = $this->end;
	if($end) {
		if($end<$start) {
			$swap = $end;
			$end = $start;
			$start = $swap;
		}
		$params = "{$start}/{$end}"; 
		$dt_check = new \DateTime($end);
	}
	else {
		$params = $start;
		$dt_check = new \DateTime($start);
	}
	
	// stop current data being cached
	$dt_now = new \DateTime;
	$format = 'Ymd'; // only check day
	if($dt_check->format($format)>=$dt_now->format($format)) {
		$params .= '?v=' . $dt_now->format('YmdHi');
	}
	# return "<p>{$params}</p>";
	
	$translate = [
		'{src}' => base_url("graph/{$controller}/{$dataname}/{$params}"),
		'{caption}' => $dataname
	];
	
	return strtr($this->template, $translate);
}

}
