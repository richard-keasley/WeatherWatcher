<?php
$request = filter_input(INPUT_SERVER, 'SCRIPT_URI');
$location = dirname($request);
header("Location: {$location}");
die;
