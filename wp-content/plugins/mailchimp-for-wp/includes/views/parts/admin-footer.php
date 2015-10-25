<?php
if( ! defined( 'MC4WP_LITE_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>
<br style="clear:both;" />

<?php if( get_locale() !== 'en_US' ) { ?>
<p class="help"><?php printf( __( 'MailChimp for WordPress is in need of translations. Is the plugin not translated in your language or do you spot errors with the current translations? Helping out is easy! Head over to <a href="%s">the translation project and click "help translate"</a>.', 'mailchimp-for-wp' ), 'https://www.transifex.com/projects/p/mailchimp-for-wordpress/' ); ?></p>
<?php } ?>

<?php if( defined( 'WP_DEBUG' ) && WP_DEBUG ) { ?>
	<p class="help">Developer? Follow or contribute to the <a href="https://github.com/ibericode/mailchimp-for-wordpress">MailChimp for WordPress project on GitHub</a>.</p>
<?php } ?>

<p class="help"><?php _e( 'This plugin is not developed by or affiliated with MailChimp in any way.', 'mailchimp-for-wp' ); ?></p>