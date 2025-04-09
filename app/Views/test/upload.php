<?php $this->extend('template');


$this->section('header'); ?>
<p>Warning: Exit development mode when you're done testing.</p>
<?php $this->endSection();

$this->section('main');
if($message) {
	echo "<p><strong>{$message}</strong></p>";
}
$action = 'upload'; # upload;
echo form_open($action);

$attrs = [
	'style' => "min-width:9em; text-align:right; display:inline-block;"
];
$format = '<p>%s %s</s>';

$inputs = array (
    'PASSKEY' => '455489773A4BCA72EDCD6992781F4E55',
    'stationtype' => 'EasyWeatherPro_V5.1.0',
    'runtime' => '3',
    'dateutc' => '2022-12-16 09:44:52',
    'tempinf' => '71.6',
    'humidityin' => '47',
    'baromrelin' => '30.180',
    'baromabsin' => '29.976',
    'tempf' => '36.0',
    'humidity' => '79',
    'winddir' => '351',
    'windspeedmph' => '3.13',
    'windgustmph' => '4.47',
    'maxdailygust' => '10.29',
    'solarradiation' => '38.83',
    'uv' => '0',
    'rainratein' => '0.000',
    'eventrainin' => '0.091',
    'hourlyrainin' => '0.000',
    'dailyrainin' => '0.000',
    'weeklyrainin' => '0.189',
    'monthlyrainin' => '0.189',
    'yearlyrainin' => '0.189',
    'totalrainin' => '0.189',
    'wh65batt' => '0',
    'freq' => '868M',
    'model' => 'WS2900_V2.01.18',
);

foreach($listener::fields as $d_sec=>$section) {
	printf('<fieldset><legend>%s</legend>', $d_sec);
	foreach($section as $d_key) {
		$s_key = $listener->map[$d_sec][$d_key] ?? null;
		if($s_key) {
			$input = [
				'name' => $s_key,
				'value' => $inputs[$s_key]
			];
			$label = humanize($d_key);
			printf($format, form_label($label, $d_key, $attrs), form_input($input));
		}
	}
	echo '</fieldset>';
}
?>
<fieldset><legend>keys</legend>
<?php
foreach($listener->get_keys() as $key=>$val) {
	$input = [
		'name' => $key,
		'value' => $val
	];
	$label = humanize($key);
	printf($format, form_label($label, $key, $attrs), form_input($input));
}
?>
</fieldset>

<p><button type="submit">OK</button></p>
<?php 
echo form_close();

# d($listener, $readings);

# echo $readings->view('current');

$this->endSection();
