<nav class="navbar"><?php
$views = ['day', 'week', 'month', 'year'];
foreach($views as $view) {
	echo anchor("dailies/{$view}/{$start}", humanize($view));
}
$views = ['custom'];
foreach($views as $view) {
	echo anchor("dailies/{$view}/{$start}/{$end}", humanize($view));
}

?></nav>