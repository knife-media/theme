 <div class="wrap">
	<form action="options.php" method="post">
	<?php
		settings_fields('knife-search-settings');
		do_settings_sections('knife-search-settings');
		submit_button();
	?>
	</form>
</div>
