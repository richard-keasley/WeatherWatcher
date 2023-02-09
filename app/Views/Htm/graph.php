<?php namespace App\Views\Htm;

class graph implements \stringable {

public $dataname = null;
public $start = null;
public $end = null;
public $template = 
'<div class="graph"><figure>
<figcaption>{caption}</figcaption>
<a href="{src}"><img src="{src}"></a>
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
	$end = $this->end;
	
	$src = ['graph', $controller, $dataname];
	if($start) $src[] = $start;
	if($end) $src[] = $end;
	$src = implode('/', $src);
		
	// stop current data being cached
	$check = max($end, $start);
	$dt_check = $check ? new \DateTime($check) : null;
	if($dt_check) {
		$dt_now = new \DateTime;
		$format = 'Ymd'; // only check day
		if($dt_check->format($format)>=$dt_now->format($format)) {
			$src .= '?v=' . $dt_now->format('YmdHi');
		}
	}
	# return "<p>{$src}</p>";
	
	$translate = [
		'{src}' => base_url($src),
		'{caption}' => $dataname
	];
	
	return strtr($this->template, $translate);
}

}
