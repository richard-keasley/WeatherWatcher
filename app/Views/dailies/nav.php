<nav class="navbar"><?php
$views = ['day', 'week', 'month', 'year', 'custom'];
foreach($views as $view) {
	echo anchor("dailies/{$view}/{$start}", humanize($view));
}
?></nav>