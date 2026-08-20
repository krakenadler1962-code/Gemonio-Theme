<?php
/**
 * Site footer.
 *
 * @package Gemonio
 */
?>
</main>
<footer class="site-footer">
	<div class="site-shell site-footer__inner">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
	</div>
</footer>
<?php $gemonio_footer_styles = function_exists( 'gemonio_get_styles' ) ? gemonio_get_styles() : array(); ?>
<?php if ( ! empty( $gemonio_footer_styles['back_to_top'] ) ) : ?>
	<button class="gemonio-back-to-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'gemonio' ); ?>" data-gemonio-back-to-top hidden>
		<span aria-hidden="true">↑</span>
	</button>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
