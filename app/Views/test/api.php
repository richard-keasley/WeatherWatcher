<?php $this->extend('template');
helper('form');
helper('inflector');

$table = new \CodeIgniter\View\Table();

$this->section('header'); ?>
<h1>Ecowitt API</h1>
<?php $this->endSection();

$this->section('main');

echo form_open();
?>
<fieldset><?php 

printf('<p>%s</p>', form_dropdown('pathkey', $api::paths, $pathkey)); 

$datetime0 = new \datetime($query['start_date']);
$datetime1 = new \datetime($query['end_date']);

$inputs = [
	[
		'name' => 'start_date',
		'type' => 'date',
		'value' => $datetime0->format('Y-m-d')
	],
	[
		'name' => 'start_time',
		'type' => 'time',
		'value' => $datetime0->format('H:i')
	],
	[
		'name' => 'end_date',
		'type' => 'date',
		'value' => $datetime1->format('Y-m-d')
	],
	[
		'name' => 'end_time',
		'type' => 'time',
		'value' => $datetime1->format('H:i')
	],
	[
		'name' => 'cycle_type',
		'value' => $query['cycle_type']
	],
	[
		'name' => 'call_back',
		'value' => $query['call_back']
	]
];
foreach($inputs as $name=>$input) {
	$label = humanize($name);
	printf('<p><label>%s</label> %s</p>', humanize($input['name']), form_input($input));
}
?></fieldset>

<fieldset>
	<button name="cmd" value="call" type="submit">call</button>
	<button name="cmd" value="daily" type="submit">daily</button>
</fieldset>
<?php echo form_close();?>

<?php 
d($api->get_data());
echo $this->include('widgets/daily');
$this->endSection();
