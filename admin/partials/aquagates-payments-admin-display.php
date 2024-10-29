<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */

?>

<div class="wrap woocommerce">
	<h1>AquaGates Setting</h1>
	<hr class="wp-header-end" />
	<?php settings_errors(); ?>
	<form id="wc-aquagates-payments-setting-form" method="post" action="" enctype="multipart/form-data">
		<div id="main-sortables" class="meta-box-sortables ui-sortable">
			<?php settings_fields( 'aquagates_payments_options' ); ?>
			<?php do_settings_sections( 'aquagates_payments_options' ); ?>
			<p class="submit">
				<?php submit_button( '', 'primary', 'save_aquagates_payments_options', false ); ?>
			</p>
		</div>
	</form>
</div>

