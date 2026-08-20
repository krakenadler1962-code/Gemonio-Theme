<?php
/**
 * Gemonio theme bootstrap.
 *
 * @package Gemonio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gemonio_setup(): void {
	load_theme_textdomain( 'gemonio', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'gemonio' ),
		)
	);
}
add_action( 'after_setup_theme', 'gemonio_setup' );

function gemonio_assets(): void {
	$theme   = wp_get_theme();
	$version = $theme->get( 'Version' );

	wp_enqueue_style(
		'gemonio-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		$version
	);

	wp_enqueue_script(
		'gemonio-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		$version,
		true
	);

	$styles = function_exists( 'gemonio_get_styles' ) ? gemonio_get_styles() : array();
	wp_localize_script(
		'gemonio-navigation',
		'gemonioNavigation',
		array(
			'scrollDuration' => isset( $styles['scroll_duration'] ) ? (int) $styles['scroll_duration'] : 700,
			'scrollEasing'   => isset( $styles['scroll_easing'] ) ? (string) $styles['scroll_easing'] : 'natural',
			'updateHash'     => ! empty( $styles['scroll_update_hash'] ),
			'compactHeader'  => ! empty( $styles['nav_compact'] ),
			'backToTop'      => ! empty( $styles['back_to_top'] ),
		)
	);

	if ( ! empty( $styles['lightbox_enabled'] ) ) {
		wp_enqueue_script(
			'gemonio-lightbox',
			get_template_directory_uri() . '/assets/js/lightbox.js',
			array(),
			$version,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'gemonio_assets' );

function gemonio_pattern_categories(): void {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
		'gemonio-content',
		array( 'label' => __( 'Gemonio content', 'gemonio' ) )
		);
	}
}
add_action( 'init', 'gemonio_pattern_categories' );

require_once get_template_directory() . '/inc/sections.php';
require_once get_template_directory() . '/inc/migration.php';
require_once get_template_directory() . '/inc/styles.php';
require_once get_template_directory() . '/inc/admin.php';
