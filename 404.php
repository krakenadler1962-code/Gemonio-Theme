<?php
/**
 * 404 template.
 *
 * @package Gemonio
 */
get_header();
?>
<div class="site-shell content-shell error-404">
	<p class="eyebrow">404</p>
	<h1><?php esc_html_e( 'Page not found.', 'gemonio' ); ?></h1>
	<p><?php esc_html_e( 'The requested page does not exist or has moved.', 'gemonio' ); ?></p>
	<a class="gemonio-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'gemonio' ); ?></a>
</div>
<?php get_footer(); ?>
