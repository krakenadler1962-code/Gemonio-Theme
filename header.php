<?php
/**
 * Site header.
 *
 * @package Gemonio
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'gemonio' ); ?></a>
<header class="site-header">
	<div class="site-shell site-header__inner">
		<div class="site-branding">
			<?php
			$gemonio_styles = function_exists( 'gemonio_get_styles' ) ? gemonio_get_styles() : array();
			$brand_mode     = isset( $gemonio_styles['brand_mode'] ) ? (string) $gemonio_styles['brand_mode'] : 'auto';
			$brand_logo_id  = isset( $gemonio_styles['brand_logo_id'] ) ? absint( $gemonio_styles['brand_logo_id'] ) : 0;
			$use_logo       = 'logo' === $brand_mode || ( 'auto' === $brand_mode && ( $brand_logo_id || has_custom_logo() ) );
			?>
			<?php if ( $use_logo && $brand_logo_id ) : ?>
				<a class="gemonio-brand-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo wp_get_attachment_image( $brand_logo_id, 'full', false, array( 'class' => 'gemonio-brand-logo', 'alt' => get_bloginfo( 'name' ) ) ); ?></a>
			<?php elseif ( $use_logo && has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
			<span class="screen-reader-text"><?php esc_html_e( 'Open navigation', 'gemonio' ); ?></span>
			<span aria-hidden="true"></span><span aria-hidden="true"></span>
		</button>

		<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'gemonio' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'primary-menu',
					'fallback_cb'    => 'gemonio_section_navigation',
					'depth'          => 1,
				)
			);
			?>
		</nav>
	</div>
</header>
<main id="content" class="site-main">
