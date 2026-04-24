<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 *  @since 1.0.0
 */
?>
<table class="wt-gc-form-table">

	<?php
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
	do_action( 'wt_gc_general_settings' );
	?>

</table>