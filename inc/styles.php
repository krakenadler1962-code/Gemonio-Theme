<?php
/**
 * Central Gemonio style system.
 *
 * Strong defaults, deliberately few controls. The starting values are based
 * on the confirmed SCRN 2.4 typography used by the migration reference site.
 *
 * @package Gemonio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the opinionated Gemonio/SCRN-inspired defaults.
 */
function gemonio_style_defaults(): array {
	return array(
		// Typography.
		'body_font'             => '"Source Sans Pro", "Helvetica Neue", Arial, sans-serif',
		'body_size'             => 16,
		'body_line_height'      => 1.65,
		'body_weight'           => 400,
		'title_font'            => '"Oswald", "Arial Narrow", Arial, sans-serif',
		'title_size'            => 60,
		'title_weight'          => 700,
		'title_line_height'     => 1.05,
		'title_transform'       => 'uppercase',
		'h3_size'               => 30,
		'h3_weight'             => 700,
		'nav_font'              => '"Source Sans Pro", "Helvetica Neue", Arial, sans-serif',
		'nav_size'              => 16,
		'nav_weight'            => 400,
		'separator_font'        => '"Source Sans Pro", "Helvetica Neue", Arial, sans-serif',
		'separator_size'        => 30,
		'separator_weight'      => 600,
		'separator_style'       => 'italic',
		'local_body_font_url'    => '',
		'local_title_font_url'   => '',

		// Colours.
		'light_bg'              => '#ffffff',
		'light_text'            => '#3d3d3d',
		'dark_bg'               => '#222222',
		'dark_text'             => '#f9f9f9',
		'mist_bg'               => '#f3f3f0',
		'heading_color'         => '#434343',
		'title_color'           => '#d64a00',
		'nav_bg'                => '#ffffff',
		'nav_text'              => '#515151',
		'nav_active_color'      => '#d64a00',
		'line_color'            => '#d8d8d3',
		'accent_color'          => '#151515',
		'separator_text_color'  => '#ffffff',

		// Sections.
		'shell_width'           => 1180,
		'content_width'         => 960,
		'section_spacing'       => 88,
		'title_spacing'         => 48,
		'title_rule'            => 1,
		'title_rule_position'   => 'both',
		'title_align'           => 'center',
		'title_rule_gap'        => 24,

		// Navigation.
		'header_height'         => 72,
		'nav_gap'               => 26,
		'nav_sticky'            => 1,
		'nav_blur'              => 0,
		'nav_compact'           => 0,
		'nav_compact_height'    => 58,
		'brand_mode'             => 'auto',
		'brand_logo_id'          => 0,
		'brand_logo_height'      => 42,
		'brand_logo_mobile'      => 36,

		// Motion.
		'scroll_duration'       => 700,
		'scroll_easing'         => 'natural',
		'scroll_update_hash'    => 1,
		'back_to_top'           => 0,

		// Buttons.
		'button_bg'             => '#151515',
		'button_text'           => '#ffffff',
		'button_radius'         => 0,
		'button_padding_y'      => 12,
		'button_padding_x'      => 20,
		'button_weight'         => 600,

		// Parallax / separators.
		'parallax_compact'      => 220,
		'parallax_normal'       => 400,
		'parallax_large'        => 620,
		'parallax_overlay'      => 18,

		// Images / lightbox.
		'lightbox_enabled'       => 1,
		'lightbox_overlay'       => '#111111',
		'lightbox_opacity'       => 92,
	);
}

/**
 * Get all saved styles merged with defaults.
 */
function gemonio_get_styles(): array {
	$saved = get_option( 'gemonio_styles', array() );
	$saved = is_array( $saved ) ? $saved : array();
	return array_merge( gemonio_style_defaults(), $saved );
}

function gemonio_color_palettes(): array {
	return array(
		'scrn-classic' => array(
			'label' => __( 'SCRN Classic', 'gemonio' ),
			'colors' => array(
				'light_bg' => '#ffffff', 'light_text' => '#3d3d3d', 'dark_bg' => '#222222', 'dark_text' => '#f9f9f9',
				'mist_bg' => '#f3f3f0', 'heading_color' => '#434343', 'title_color' => '#d64a00', 'nav_bg' => '#ffffff',
				'nav_text' => '#515151', 'nav_active_color' => '#d64a00', 'line_color' => '#d8d8d3', 'accent_color' => '#151515', 'separator_text_color' => '#ffffff',
			),
		),
		'warm-editorial' => array(
			'label' => __( 'Warm Editorial', 'gemonio' ),
			'colors' => array(
				'light_bg' => '#fbf7f2', 'light_text' => '#3b312b', 'dark_bg' => '#2b2522', 'dark_text' => '#faf4ee',
				'mist_bg' => '#efe5db', 'heading_color' => '#44362f', 'title_color' => '#b94f30', 'nav_bg' => '#fbf7f2',
				'nav_text' => '#554840', 'nav_active_color' => '#b94f30', 'line_color' => '#d9c9bd', 'accent_color' => '#813923', 'separator_text_color' => '#ffffff',
			),
		),
		'monochrome' => array(
			'label' => __( 'Monochrome', 'gemonio' ),
			'colors' => array(
				'light_bg' => '#ffffff', 'light_text' => '#262626', 'dark_bg' => '#111111', 'dark_text' => '#f7f7f7',
				'mist_bg' => '#f2f2f2', 'heading_color' => '#171717', 'title_color' => '#171717', 'nav_bg' => '#ffffff',
				'nav_text' => '#3a3a3a', 'nav_active_color' => '#171717', 'line_color' => '#d4d4d4', 'accent_color' => '#111111', 'separator_text_color' => '#ffffff',
			),
		),
		'sage' => array(
			'label' => __( 'Sage', 'gemonio' ),
			'colors' => array(
				'light_bg' => '#f8f9f4', 'light_text' => '#344038', 'dark_bg' => '#26352e', 'dark_text' => '#f5f7f2',
				'mist_bg' => '#edf0e7', 'heading_color' => '#31423a', 'title_color' => '#737958', 'nav_bg' => '#f8f9f4',
				'nav_text' => '#3f4b43', 'nav_active_color' => '#737958', 'line_color' => '#cfd5c5', 'accent_color' => '#5d674d', 'separator_text_color' => '#ffffff',
			),
		),
		'midnight' => array(
			'label' => __( 'Midnight', 'gemonio' ),
			'colors' => array(
				'light_bg' => '#f6f5f1', 'light_text' => '#2a303a', 'dark_bg' => '#151923', 'dark_text' => '#f6f5f1',
				'mist_bg' => '#e9e9ec', 'heading_color' => '#202631', 'title_color' => '#9d533f', 'nav_bg' => '#f6f5f1',
				'nav_text' => '#343b47', 'nav_active_color' => '#9d533f', 'line_color' => '#c9cbd0', 'accent_color' => '#27334a', 'separator_text_color' => '#ffffff',
			),
		),
	);
}

function gemonio_style_groups(): array {
	return array(
		'typography' => array(
			'label'       => __( 'Typografie', 'gemonio' ),
			'icon'        => 'editor-textcolor',
			'description' => __( 'Grundschrift, Section-Titel, Navigation und Separator-Texte.', 'gemonio' ),
			'keys'        => array( 'body_font', 'body_size', 'body_line_height', 'body_weight', 'title_font', 'title_size', 'title_weight', 'title_line_height', 'title_transform', 'h3_size', 'h3_weight', 'nav_font', 'nav_size', 'nav_weight', 'separator_font', 'separator_size', 'separator_weight', 'separator_style', 'local_body_font_url', 'local_title_font_url' ),
		),
		'colors' => array(
			'label'       => __( 'Farben', 'gemonio' ),
			'icon'        => 'art',
			'description' => __( 'Helle, dunkle und graue Sections sowie Akzent-, Titel- und Linienfarben.', 'gemonio' ),
			'keys'        => array( 'light_bg', 'light_text', 'dark_bg', 'dark_text', 'mist_bg', 'heading_color', 'title_color', 'nav_bg', 'nav_text', 'nav_active_color', 'line_color', 'accent_color', 'separator_text_color' ),
		),
		'sections' => array(
			'label'       => __( 'Sections', 'gemonio' ),
			'icon'        => 'layout',
			'description' => __( 'Breiten, Rhythmus, Titelabstände und Trennlinien.', 'gemonio' ),
			'keys'        => array( 'shell_width', 'content_width', 'section_spacing', 'title_spacing', 'title_rule', 'title_rule_position', 'title_align', 'title_rule_gap' ),
		),
		'navigation' => array(
			'label'       => __( 'Navigation', 'gemonio' ),
			'icon'        => 'menu',
			'description' => __( 'Höhe, Abstände und Verhalten des One-Page-Menüs.', 'gemonio' ),
			'keys'        => array( 'header_height', 'nav_gap', 'nav_sticky', 'nav_blur', 'nav_compact', 'nav_compact_height', 'brand_mode', 'brand_logo_id', 'brand_logo_height', 'brand_logo_mobile' ),
		),
		'motion' => array(
			'label'       => __( 'Bewegung', 'gemonio' ),
			'icon'        => 'controls-play',
			'description' => __( 'Scrollgefühl und kleine One-Page-Komfortfunktionen.', 'gemonio' ),
			'keys'        => array( 'scroll_duration', 'scroll_easing', 'scroll_update_hash', 'back_to_top' ),
		),
		'buttons' => array(
			'label'       => __( 'Buttons', 'gemonio' ),
			'icon'        => 'button',
			'description' => __( 'Ein globaler Button-Stil statt Einzelgestaltung pro Element.', 'gemonio' ),
			'keys'        => array( 'button_bg', 'button_text', 'button_radius', 'button_padding_y', 'button_padding_x', 'button_weight' ),
		),
		'media' => array(
			'label'       => __( 'Bilder / Lightbox', 'gemonio' ),
			'icon'        => 'format-gallery',
			'description' => __( 'Bilder gross öffnen – schlank, nativ und ohne externe Library.', 'gemonio' ),
			'keys'        => array( 'lightbox_enabled', 'lightbox_overlay', 'lightbox_opacity' ),
		),
		'parallax' => array(
			'label'       => __( 'Parallax / Separatoren', 'gemonio' ),
			'icon'        => 'format-image',
			'description' => __( 'Das visuelle Herzstück zwischen den Sections: Höhe und Overlay.', 'gemonio' ),
			'keys'        => array( 'parallax_compact', 'parallax_normal', 'parallax_large', 'parallax_overlay' ),
		),
		'advanced' => array(
			'label'       => __( 'Erweitert', 'gemonio' ),
			'icon'        => 'editor-code',
			'description' => __( 'Additional CSS für gezielte Overrides. Die normalen Styles bleiben die erste Wahl.', 'gemonio' ),
			'keys'        => array(),
		),
	);
}

function gemonio_style_int( $value, int $min, int $max ): int {
	$value = (int) $value;
	return max( $min, min( $max, $value ) );
}

function gemonio_style_float( $value, float $min, float $max ): float {
	$value = (float) $value;
	return max( $min, min( $max, $value ) );
}

function gemonio_style_font_stack( $value ): string {
	$value = sanitize_text_field( (string) $value );
	$value = str_replace( array( '<', '>', '{', '}', ';' ), '', $value );
	return '' !== trim( $value ) ? $value : '-apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif';
}

function gemonio_sanitize_style_value( string $key, $value ) {
	$color_keys = array( 'light_bg', 'light_text', 'dark_bg', 'dark_text', 'mist_bg', 'heading_color', 'title_color', 'nav_bg', 'nav_text', 'nav_active_color', 'line_color', 'accent_color', 'separator_text_color', 'button_bg', 'button_text', 'lightbox_overlay' );
	if ( in_array( $key, $color_keys, true ) ) {
		$color = sanitize_hex_color( (string) $value );
		return $color ? strtolower( $color ) : gemonio_style_defaults()[ $key ];
	}

	if ( in_array( $key, array( 'body_font', 'title_font', 'nav_font', 'separator_font' ), true ) ) {
		return gemonio_style_font_stack( $value );
	}

	if ( 'body_line_height' === $key ) {
		return gemonio_style_float( $value, 1.1, 2.2 );
	}
	if ( 'title_line_height' === $key ) {
		return gemonio_style_float( $value, 0.8, 1.8 );
	}
	if ( 'title_transform' === $key ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'none', 'uppercase' ), true ) ? $value : 'uppercase';
	}
	if ( 'title_align' === $key ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'left', 'center', 'right' ), true ) ? $value : 'center';
	}
	if ( 'title_rule_position' === $key ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'both', 'top', 'bottom' ), true ) ? $value : 'both';
	}
	if ( 'separator_style' === $key ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'normal', 'italic' ), true ) ? $value : 'italic';
	}
	if ( in_array( $key, array( 'local_body_font_url', 'local_title_font_url' ), true ) ) {
		return esc_url_raw( (string) $value );
	}
	if ( 'brand_mode' === $key ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'auto', 'title', 'logo' ), true ) ? $value : 'auto';
	}
	if ( 'brand_logo_id' === $key ) {
		return absint( $value );
	}
	if ( 'scroll_easing' === $key ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'natural', 'soft', 'linear' ), true ) ? $value : 'natural';
	}
	if ( in_array( $key, array( 'title_rule', 'nav_sticky', 'nav_blur', 'nav_compact', 'scroll_update_hash', 'back_to_top', 'lightbox_enabled' ), true ) ) {
		return ! empty( $value ) ? 1 : 0;
	}

	$ranges = array(
		'body_size'          => array( 12, 24 ),
		'body_weight'        => array( 300, 800 ),
		'title_size'         => array( 30, 110 ),
		'title_weight'       => array( 300, 900 ),
		'h3_size'            => array( 18, 60 ),
		'h3_weight'          => array( 300, 900 ),
		'nav_size'           => array( 11, 24 ),
		'nav_weight'         => array( 300, 800 ),
		'separator_size'     => array( 16, 72 ),
		'separator_weight'   => array( 300, 900 ),
		'shell_width'        => array( 900, 1600 ),
		'content_width'      => array( 560, 1200 ),
		'section_spacing'    => array( 30, 180 ),
		'title_spacing'      => array( 16, 100 ),
		'title_rule_gap'     => array( 10, 80 ),
		'header_height'      => array( 52, 120 ),
		'nav_gap'            => array( 8, 60 ),
		'nav_compact_height' => array( 46, 90 ),
		'brand_logo_height'   => array( 18, 100 ),
		'brand_logo_mobile'   => array( 18, 80 ),
		'scroll_duration'    => array( 0, 1400 ),
		'button_radius'      => array( 0, 50 ),
		'button_padding_y'   => array( 6, 28 ),
		'button_padding_x'   => array( 10, 50 ),
		'button_weight'      => array( 300, 900 ),
		'parallax_compact'   => array( 120, 500 ),
		'parallax_normal'    => array( 180, 800 ),
		'parallax_large'     => array( 260, 1000 ),
		'parallax_overlay'   => array( 0, 70 ),
		'lightbox_opacity'   => array( 50, 100 ),
	);

	if ( isset( $ranges[ $key ] ) ) {
		return gemonio_style_int( $value, $ranges[ $key ][0], $ranges[ $key ][1] );
	}

	return sanitize_text_field( (string) $value );
}

/**
 * CSS variables and a few state rules generated from global style settings.
 */
function gemonio_custom_style_css(): string {
	$s = gemonio_get_styles();
	$overlay = number_format( (int) $s['parallax_overlay'] / 100, 2, '.', '' );

	$font_css = '';
	$body_font = $s['body_font'];
	$title_font = $s['title_font'];
	$nav_font = $s['nav_font'];
	$separator_font = $s['separator_font'];
	if ( ! empty( $s['local_body_font_url'] ) ) {
		$url = esc_url_raw( $s['local_body_font_url'] );
		$font_css .= '@font-face{font-family:"Gemonio Local Text";src:url("' . esc_url( $url ) . '") format("woff2");font-style:normal;font-weight:100 900;font-display:swap;}';
		$body_font = '"Gemonio Local Text",' . $body_font;
		$nav_font = '"Gemonio Local Text",' . $nav_font;
		$separator_font = '"Gemonio Local Text",' . $separator_font;
	}
	if ( ! empty( $s['local_title_font_url'] ) ) {
		$url = esc_url_raw( $s['local_title_font_url'] );
		$font_css .= '@font-face{font-family:"Gemonio Local Display";src:url("' . esc_url( $url ) . '") format("woff2");font-style:normal;font-weight:100 900;font-display:swap;}';
		$title_font = '"Gemonio Local Display",' . $title_font;
	}

	$css = $font_css . ':root{';
	$css .= '--gemonio-shell:min(' . (int) $s['shell_width'] . 'px,calc(100vw - 48px));';
	$css .= '--gemonio-content:min(' . (int) $s['content_width'] . 'px,calc(100vw - 48px));';
	$css .= '--gemonio-section-y:' . (int) $s['section_spacing'] . 'px;';
	$css .= '--gemonio-title-space:' . (int) $s['title_spacing'] . 'px;';
	$css .= '--gemonio-title-rule-gap:' . (int) $s['title_rule_gap'] . 'px;';
	$css .= '--gemonio-title-align:' . $s['title_align'] . ';';
	$css .= '--gemonio-header-height:' . (int) $s['header_height'] . 'px;';
	$css .= '--gemonio-line:' . $s['line_color'] . ';';
	$css .= '--gemonio-ink:' . $s['light_text'] . ';';
	$css .= '--gemonio-paper:' . $s['light_bg'] . ';';
	$css .= '--gemonio-dark:' . $s['dark_bg'] . ';';
	$css .= '--gemonio-dark-text:' . $s['dark_text'] . ';';
	$css .= '--gemonio-mist:' . $s['mist_bg'] . ';';
	$css .= '--gemonio-heading:' . $s['heading_color'] . ';';
	$css .= '--gemonio-title-color:' . $s['title_color'] . ';';
	$css .= '--gemonio-accent:' . $s['accent_color'] . ';';
	$css .= '--gemonio-body-font:' . $body_font . ';';
	$css .= '--gemonio-body-size:' . (int) $s['body_size'] . 'px;';
	$css .= '--gemonio-body-line:' . (float) $s['body_line_height'] . ';';
	$css .= '--gemonio-body-weight:' . (int) $s['body_weight'] . ';';
	$css .= '--gemonio-title-font:' . $title_font . ';';
	$css .= '--gemonio-title-size:' . (int) $s['title_size'] . 'px;';
	$css .= '--gemonio-title-weight:' . (int) $s['title_weight'] . ';';
	$css .= '--gemonio-title-line:' . (float) $s['title_line_height'] . ';';
	$css .= '--gemonio-title-transform:' . $s['title_transform'] . ';';
	$css .= '--gemonio-h3-size:' . (int) $s['h3_size'] . 'px;';
	$css .= '--gemonio-h3-weight:' . (int) $s['h3_weight'] . ';';
	$css .= '--gemonio-nav-font:' . $nav_font . ';';
	$css .= '--gemonio-nav-size:' . (int) $s['nav_size'] . 'px;';
	$css .= '--gemonio-nav-weight:' . (int) $s['nav_weight'] . ';';
	$css .= '--gemonio-nav-gap:' . (int) $s['nav_gap'] . 'px;';
	$css .= '--gemonio-nav-bg:' . $s['nav_bg'] . ';';
	$css .= '--gemonio-nav-text:' . $s['nav_text'] . ';';
	$css .= '--gemonio-nav-active:' . $s['nav_active_color'] . ';';
	$css .= '--gemonio-header-compact-height:' . (int) $s['nav_compact_height'] . 'px;';
	$css .= '--gemonio-logo-height:' . (int) $s['brand_logo_height'] . 'px;';
	$css .= '--gemonio-logo-mobile-height:' . (int) $s['brand_logo_mobile'] . 'px;';
	$css .= '--gemonio-button-bg:' . $s['button_bg'] . ';';
	$css .= '--gemonio-button-text:' . $s['button_text'] . ';';
	$css .= '--gemonio-button-radius:' . (int) $s['button_radius'] . 'px;';
	$css .= '--gemonio-button-py:' . (int) $s['button_padding_y'] . 'px;';
	$css .= '--gemonio-button-px:' . (int) $s['button_padding_x'] . 'px;';
	$css .= '--gemonio-button-weight:' . (int) $s['button_weight'] . ';';
	$css .= '--gemonio-separator-font:' . $separator_font . ';';
	$css .= '--gemonio-separator-size:' . (int) $s['separator_size'] . 'px;';
	$css .= '--gemonio-separator-weight:' . (int) $s['separator_weight'] . ';';
	$css .= '--gemonio-separator-style:' . $s['separator_style'] . ';';
	$css .= '--gemonio-separator-text:' . $s['separator_text_color'] . ';';
	$css .= '--gemonio-parallax-compact:' . (int) $s['parallax_compact'] . 'px;';
	$css .= '--gemonio-parallax-normal:' . (int) $s['parallax_normal'] . 'px;';
	$css .= '--gemonio-parallax-large:' . (int) $s['parallax_large'] . 'px;';
	$css .= '--gemonio-parallax-overlay:' . $overlay . ';';
	$css .= '--gemonio-lightbox-overlay:' . $s['lightbox_overlay'] . ';';
	$css .= '--gemonio-lightbox-opacity:' . (int) $s['lightbox_opacity'] . '%;';
	$css .= '}';

	if ( empty( $s['title_rule'] ) ) {
		$css .= '.gemonio-section-title::before,.gemonio-section-title::after{display:none!important;}';
	} elseif ( 'top' === $s['title_rule_position'] ) {
		$css .= '.gemonio-section-title::after{display:none!important;}';
	} elseif ( 'bottom' === $s['title_rule_position'] ) {
		$css .= '.gemonio-section-title::before{display:none!important;}';
	}
	if ( 'left' === $s['title_align'] ) {
		$css .= '.gemonio-section-subtitle{margin-left:0;margin-right:auto;}';
	} elseif ( 'right' === $s['title_align'] ) {
		$css .= '.gemonio-section-subtitle{margin-left:auto;margin-right:0;}';
	}

	if ( empty( $s['nav_sticky'] ) ) {
		$css .= '.site-header{position:relative!important;top:auto!important;}';
	}
	if ( empty( $s['nav_blur'] ) ) {
		$css .= '.site-header{backdrop-filter:none!important;}';
	}
	if ( empty( $s['nav_compact'] ) ) {
		$css .= '.site-header.is-scrolled .site-header__inner{min-height:var(--gemonio-header-height)!important;}';
	}

	return $css;
}

/**
 * Add global CSS after main.css.
 */
function gemonio_enqueue_custom_styles(): void {
	if ( wp_style_is( 'gemonio-main', 'enqueued' ) || wp_style_is( 'gemonio-main', 'registered' ) ) {
		wp_add_inline_style( 'gemonio-main', gemonio_custom_style_css() );
	}
}
add_action( 'wp_enqueue_scripts', 'gemonio_enqueue_custom_styles', 20 );

/**
 * Styles submenu.
 */
function gemonio_styles_admin_menu(): void {
	add_submenu_page(
		'gemonio-theme',
		__( 'Styles', 'gemonio' ),
		__( 'Styles', 'gemonio' ),
		'manage_options',
		'gemonio-styles',
		'gemonio_styles_page'
	);
}
add_action( 'admin_menu', 'gemonio_styles_admin_menu', 10 );

function gemonio_allow_local_font_mime( array $mimes ): array {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['woff2'] = 'font/woff2';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'gemonio_allow_local_font_mime' );

function gemonio_styles_admin_assets( string $hook ): void {
	if ( 'gemonio_page_gemonio-styles' !== $hook ) {
		return;
	}
	$theme = wp_get_theme();
	wp_enqueue_media();
	$code_settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
	wp_enqueue_script( 'wp-theme-plugin-editor' );
	wp_enqueue_style( 'wp-codemirror' );
	wp_enqueue_script(
		'gemonio-admin-styles',
		get_template_directory_uri() . '/assets/js/admin-styles.js',
		array(),
		$theme->get( 'Version' ),
		true
	);
	if ( is_array( $code_settings ) ) {
		wp_add_inline_script( 'gemonio-admin-styles', 'window.gemonioCodeEditorSettings = ' . wp_json_encode( $code_settings ) . ';', 'before' );
	}
}
add_action( 'admin_enqueue_scripts', 'gemonio_styles_admin_assets' );

/**
 * Save or reset one style group without touching the others.
 */
function gemonio_handle_save_styles(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gemonio' ) );
	}

	check_admin_referer( 'gemonio_save_styles' );
	$groups = gemonio_style_groups();
	$tab    = isset( $_POST['gemonio_style_tab'] ) ? sanitize_key( wp_unslash( $_POST['gemonio_style_tab'] ) ) : 'typography';
	$tab    = isset( $groups[ $tab ] ) ? $tab : 'typography';
	$styles = gemonio_get_styles();
	$defs   = gemonio_style_defaults();

	if ( isset( $_POST['gemonio_reset_all'] ) ) {
		$styles  = $defs;
		$message = 'reset-all';
	} elseif ( isset( $_POST['gemonio_reset_group'] ) ) {
		foreach ( $groups[ $tab ]['keys'] as $key ) {
			$styles[ $key ] = $defs[ $key ];
		}
		$message = 'reset';
	} else {
		$posted = isset( $_POST['gemonio_styles'] ) && is_array( $_POST['gemonio_styles'] ) ? wp_unslash( $_POST['gemonio_styles'] ) : array();
		foreach ( $groups[ $tab ]['keys'] as $key ) {
			// Checkbox values are intentionally absent when off.
			$value = isset( $posted[ $key ] ) ? $posted[ $key ] : 0;
			$styles[ $key ] = gemonio_sanitize_style_value( $key, $value );
		}
		$message = 'saved';
	}

	if ( 'advanced' === $tab ) {
		$custom_css = isset( $_POST['gemonio_custom_css'] ) ? wp_unslash( $_POST['gemonio_custom_css'] ) : '';
		if ( isset( $_POST['gemonio_clear_custom_css'] ) ) {
			$custom_css = '';
			$message = 'css-cleared';
		}
		if ( function_exists( 'wp_update_custom_css_post' ) ) {
			wp_update_custom_css_post( $custom_css );
		}
	}

	update_option( 'gemonio_styles', $styles, false );
	wp_safe_redirect( add_query_arg( array( 'page' => 'gemonio-styles', 'tab' => $tab, 'gemonio-message' => $message ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_gemonio_save_styles', 'gemonio_handle_save_styles' );

function gemonio_style_number_field( string $key, string $label, $value, int $min, int $max, string $unit = 'px', string $description = '' ): void {
	?>
	<div class="gemonio-style-field" data-gemonio-control="<?php echo esc_attr( $key ); ?>">
		<label for="gemonio-style-<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<div class="gemonio-style-range-row">
			<input class="gemonio-style-range" type="range" value="<?php echo esc_attr( (string) $value ); ?>" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" step="1" data-gemonio-range-for="gemonio-style-<?php echo esc_attr( $key ); ?>">
			<div class="gemonio-style-number">
				<input id="gemonio-style-<?php echo esc_attr( $key ); ?>" type="number" name="gemonio_styles[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( (string) $value ); ?>" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" step="1" data-gemonio-preview-key="<?php echo esc_attr( $key ); ?>">
				<?php if ( '' !== $unit ) : ?><span><?php echo esc_html( $unit ); ?></span><?php endif; ?>
			</div>
		</div>
		<?php if ( $description ) : ?><p class="description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	</div>
	<?php
}

function gemonio_style_color_field( string $key, string $label, string $value ): void {
	?>
	<div class="gemonio-style-field gemonio-style-field--color" data-gemonio-control="<?php echo esc_attr( $key ); ?>">
		<label for="gemonio-style-<?php echo esc_attr( $key ); ?>-text"><strong><?php echo esc_html( $label ); ?></strong></label>
		<div class="gemonio-style-color" data-gemonio-color-control data-gemonio-shade-base="<?php echo esc_attr( $value ); ?>">
			<input id="gemonio-style-<?php echo esc_attr( $key ); ?>" type="hidden" name="gemonio_styles[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" data-gemonio-color-key="<?php echo esc_attr( $key ); ?>">
			<button type="button" class="gemonio-style-color-current" data-gemonio-color-current data-gemonio-color-toggle style="--gemonio-current-color:<?php echo esc_attr( $value ); ?>" aria-expanded="false" aria-controls="gemonio-shades-<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr( sprintf( __( '%s auswählen', 'gemonio' ), $label ) ); ?>"><span class="screen-reader-text"><?php echo esc_html( sprintf( __( '%s auswählen', 'gemonio' ), $label ) ); ?></span></button>
			<input id="gemonio-style-<?php echo esc_attr( $key ); ?>-text" class="gemonio-style-color-text" type="text" value="<?php echo esc_attr( strtoupper( $value ) ); ?>" maxlength="7" spellcheck="false" autocomplete="off" data-gemonio-color-text aria-label="<?php echo esc_attr( sprintf( __( '%s als Hex-Farbwert', 'gemonio' ), $label ) ); ?>">
			<div id="gemonio-shades-<?php echo esc_attr( $key ); ?>" class="gemonio-color-shades" data-gemonio-color-shades aria-label="<?php echo esc_attr( sprintf( __( 'Farbabstufungen für %s', 'gemonio' ), $label ) ); ?>" hidden></div>
		</div>
	</div>
	<?php
}

function gemonio_style_weight_field( string $key, string $label, int $value ): void {
	$weights = array( 300 => __( '300 – Light', 'gemonio' ), 400 => __( '400 – Regular', 'gemonio' ), 500 => __( '500 – Medium', 'gemonio' ), 600 => __( '600 – Semibold', 'gemonio' ), 700 => __( '700 – Bold', 'gemonio' ), 800 => __( '800 – Extra Bold', 'gemonio' ), 900 => __( '900 – Black', 'gemonio' ) );
	?>
	<div class="gemonio-style-field" data-gemonio-control="<?php echo esc_attr( $key ); ?>">
		<label for="gemonio-style-<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<select id="gemonio-style-<?php echo esc_attr( $key ); ?>" name="gemonio_styles[<?php echo esc_attr( $key ); ?>]" data-gemonio-preview-key="<?php echo esc_attr( $key ); ?>">
			<?php foreach ( $weights as $weight => $weight_label ) : ?>
				<option value="<?php echo esc_attr( (string) $weight ); ?>" <?php selected( $value, $weight ); ?>><?php echo esc_html( $weight_label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
}

function gemonio_style_font_file_field( string $key, string $label, string $value, string $description = '' ): void {
	?>
	<div class="gemonio-style-field gemonio-style-field--wide gemonio-font-file" data-gemonio-font-file>
		<label for="gemonio-style-<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<div class="gemonio-font-file__row">
			<input id="gemonio-style-<?php echo esc_attr( $key ); ?>" class="widefat" type="url" name="gemonio_styles[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="https://…/font.woff2" data-gemonio-font-url>
			<button type="button" class="button" data-gemonio-font-select><?php esc_html_e( 'WOFF2 wählen', 'gemonio' ); ?></button>
			<button type="button" class="button-link-delete" data-gemonio-font-remove <?php echo $value ? '' : 'hidden'; ?>><?php esc_html_e( 'Entfernen', 'gemonio' ); ?></button>
		</div>
		<?php if ( $description ) : ?><p class="description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	</div>
	<?php
}

function gemonio_active_palette( array $styles ): string {
	foreach ( gemonio_color_palettes() as $slug => $palette ) {
		$matches = true;
		foreach ( $palette['colors'] as $key => $value ) {
			if ( strtolower( (string) ( $styles[ $key ] ?? '' ) ) !== strtolower( $value ) ) { $matches = false; break; }
		}
		if ( $matches ) { return $slug; }
	}
	return 'custom';
}

function gemonio_color_palette_picker( array $styles ): void {
	$active = gemonio_active_palette( $styles );
	?>
	<div class="gemonio-style-subsection gemonio-style-subsection--palettes">
		<h3><?php esc_html_e( 'Farbpaletten', 'gemonio' ); ?></h3>
		<p><?php esc_html_e( 'Eine stimmige Basis mit einem Klick. Danach bleibt jeder Farbwert frei anpassbar.', 'gemonio' ); ?></p>
		<div class="gemonio-palette-grid" data-gemonio-palettes>
			<?php foreach ( gemonio_color_palettes() as $slug => $palette ) : $colors = $palette['colors']; ?>
				<button type="button" class="gemonio-palette <?php echo $slug === $active ? 'is-active' : ''; ?>" data-gemonio-palette="<?php echo esc_attr( wp_json_encode( $colors ) ); ?>" aria-pressed="<?php echo $slug === $active ? 'true' : 'false'; ?>">
					<span class="gemonio-palette__swatches" aria-hidden="true">
						<i style="background:<?php echo esc_attr( $colors['light_bg'] ); ?>"></i><i style="background:<?php echo esc_attr( $colors['title_color'] ); ?>"></i><i style="background:<?php echo esc_attr( $colors['accent_color'] ); ?>"></i><i style="background:<?php echo esc_attr( $colors['dark_bg'] ); ?>"></i>
					</span>
					<strong><?php echo esc_html( $palette['label'] ); ?></strong>
				</button>
			<?php endforeach; ?>
			<div class="gemonio-palette gemonio-palette--custom <?php echo 'custom' === $active ? 'is-active' : ''; ?>" data-gemonio-custom-palette><span class="dashicons dashicons-admin-customizer"></span><strong><?php esc_html_e( 'Custom', 'gemonio' ); ?></strong></div>
		</div>
	</div>
	<?php
}

function gemonio_styles_preview( array $s ): void {
	?>
	<div class="gemonio-style-preview" data-gemonio-style-preview style="--preview-light-bg:<?php echo esc_attr( $s['light_bg'] ); ?>;--preview-light-text:<?php echo esc_attr( $s['light_text'] ); ?>;--preview-heading:<?php echo esc_attr( $s['heading_color'] ); ?>;--preview-title-color:<?php echo esc_attr( $s['title_color'] ); ?>;--preview-line-color:<?php echo esc_attr( $s['line_color'] ); ?>;--preview-nav-bg:<?php echo esc_attr( $s['nav_bg'] ); ?>;--preview-nav-text:<?php echo esc_attr( $s['nav_text'] ); ?>;--preview-nav-active:<?php echo esc_attr( $s['nav_active_color'] ); ?>;--preview-button-bg:<?php echo esc_attr( $s['button_bg'] ); ?>;--preview-button-text:<?php echo esc_attr( $s['button_text'] ); ?>;--preview-button-radius:<?php echo (int) $s['button_radius']; ?>px;--preview-title-size:<?php echo (int) $s['title_size']; ?>px;--preview-title-weight:<?php echo (int) $s['title_weight']; ?>;--preview-body-size:<?php echo (int) $s['body_size']; ?>px;--preview-body-weight:<?php echo (int) $s['body_weight']; ?>;--preview-h3-size:<?php echo (int) $s['h3_size']; ?>px;--preview-h3-weight:<?php echo (int) $s['h3_weight']; ?>;--preview-nav-weight:<?php echo (int) $s['nav_weight']; ?>;--preview-parallax-overlay:<?php echo esc_attr( number_format( (int) $s['parallax_overlay'] / 100, 2, '.', '' ) ); ?>;--preview-body-font:<?php echo esc_attr( $s['body_font'] ); ?>;--preview-title-font:<?php echo esc_attr( $s['title_font'] ); ?>;">
		<div class="gemonio-style-preview__nav"><strong>Gemonio</strong><span>ABOUT&nbsp;&nbsp;&nbsp; WORK&nbsp;&nbsp;&nbsp; CONTACT</span></div>
		<div class="gemonio-style-preview__section">
			<div class="gemonio-style-preview__title" style="text-align:<?php echo esc_attr( $s['title_align'] ); ?>">
				<i data-preview-title-line="top" <?php echo empty( $s['title_rule'] ) || ! in_array( $s['title_rule_position'], array( 'both', 'top' ), true ) ? 'hidden' : ''; ?>></i>
				<span>AUSSTELLUNG</span>
				<em>«Protagonisten der Schweizer Wohnkultur»</em>
				<i data-preview-title-line="bottom" <?php echo empty( $s['title_rule'] ) || ! in_array( $s['title_rule_position'], array( 'both', 'bottom' ), true ) ? 'hidden' : ''; ?>></i>
			</div>
			<h3>Schlicht, klar und schon gestaltet.</h3>
			<p>Die Styles sind global definiert. Eine Section braucht ihren Inhalt – nicht zwanzig Designentscheidungen.</p>
			<a href="#" onclick="return false;">Mehr erfahren</a>
		</div>
		<div class="gemonio-style-preview__parallax"><span>Ein ruhiger Übergang zwischen den Bereichen.</span></div>
	</div>
	<?php
}

function gemonio_styles_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$groups = gemonio_style_groups();
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'typography';
	$tab    = isset( $groups[ $tab ] ) ? $tab : 'typography';
	$s      = gemonio_get_styles();
	?>
	<div class="wrap gemonio-admin-wrap gemonio-styles-wrap">
		<div class="gemonio-styles-head">
			<div>
				<h1><?php esc_html_e( 'Gemonio Styles', 'gemonio' ); ?></h1>
				<p><?php esc_html_e( 'Starke SCRN-inspirierte Defaults. Zentral anpassen, einzelne Sections bewusst simpel halten.', 'gemonio' ); ?></p>
			</div>
			<div class="gemonio-style-preset"><span><?php esc_html_e( 'Basis', 'gemonio' ); ?></span><strong>SCRN Classic</strong><small><?php esc_html_e( 'Gemonio modernisiert die Technik, nicht die gute Design-DNA.', 'gemonio' ); ?></small></div>
		</div>

		<?php if ( isset( $_GET['gemonio-message'] ) ) : ?>
			<?php $msg = sanitize_key( wp_unslash( $_GET['gemonio-message'] ) ); ?>
			<div class="notice notice-success is-dismissible"><p><?php echo 'reset-all' === $msg ? esc_html__( 'Alle Styles wurden auf SCRN Classic zurückgesetzt.', 'gemonio' ) : ( 'reset' === $msg ? esc_html__( 'Dieser Style-Bereich wurde zurückgesetzt.', 'gemonio' ) : esc_html__( 'Styles gespeichert.', 'gemonio' ) ); ?></p></div>
		<?php endif; ?>

		<div class="gemonio-style-workspace">
			<nav class="gemonio-style-sidebar" aria-label="<?php esc_attr_e( 'Style-Bereiche', 'gemonio' ); ?>">
				<div class="gemonio-style-sidebar__title"><?php esc_html_e( 'Design', 'gemonio' ); ?></div>
				<?php foreach ( $groups as $slug => $group ) : ?>
					<a class="gemonio-style-navitem <?php echo $slug === $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'gemonio-styles', 'tab' => $slug ), admin_url( 'admin.php' ) ) ); ?>">
						<span class="dashicons dashicons-<?php echo esc_attr( $group['icon'] ); ?>"></span>
						<span><strong><?php echo esc_html( $group['label'] ); ?></strong><small><?php echo esc_html( $group['description'] ); ?></small></span>
					</a>
				<?php endforeach; ?>
			</nav>

			<form class="gemonio-style-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-gemonio-style-form>
				<input type="hidden" name="action" value="gemonio_save_styles">
				<input type="hidden" name="gemonio_style_tab" value="<?php echo esc_attr( $tab ); ?>">
				<?php wp_nonce_field( 'gemonio_save_styles' ); ?>
				<div class="gemonio-style-card">
					<div class="gemonio-style-card__head"><div><h2><?php echo esc_html( $groups[ $tab ]['label'] ); ?></h2><p><?php echo esc_html( $groups[ $tab ]['description'] ); ?></p></div><span class="gemonio-unsaved" data-gemonio-unsaved hidden><?php esc_html_e( 'Ungespeichert', 'gemonio' ); ?></span></div>

					<?php if ( 'typography' === $tab ) : ?>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Grundtext', 'gemonio' ); ?></h3><div class="gemonio-style-field gemonio-style-field--wide"><label><strong><?php esc_html_e( 'Schriftfamilie / Font-Stack', 'gemonio' ); ?></strong></label><input class="widefat" type="text" name="gemonio_styles[body_font]" value="<?php echo esc_attr( $s['body_font'] ); ?>"></div><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'body_size', __( 'Schriftgrösse', 'gemonio' ), $s['body_size'], 12, 24 ); ?><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Zeilenhöhe', 'gemonio' ); ?></strong></label><input type="number" step="0.05" min="1.1" max="2.2" name="gemonio_styles[body_line_height]" value="<?php echo esc_attr( (string) $s['body_line_height'] ); ?>"></div><?php gemonio_style_weight_field( 'body_weight', __( 'Schriftgewicht', 'gemonio' ), (int) $s['body_weight'] ); ?></div></div>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Section-Titel', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Der zentrale SCRN-Look: gross, ruhig, zentriert und klar vom Inhalt getrennt.', 'gemonio' ); ?></p><div class="gemonio-style-field gemonio-style-field--wide"><label><strong><?php esc_html_e( 'Schriftfamilie / Font-Stack', 'gemonio' ); ?></strong></label><input class="widefat" type="text" name="gemonio_styles[title_font]" value="<?php echo esc_attr( $s['title_font'] ); ?>"></div><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'title_size', __( 'Titelgrösse', 'gemonio' ), $s['title_size'], 30, 110 ); ?><?php gemonio_style_weight_field( 'title_weight', __( 'Titelgewicht', 'gemonio' ), (int) $s['title_weight'] ); ?><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Zeilenhöhe', 'gemonio' ); ?></strong></label><input type="number" step="0.05" min="0.8" max="1.8" name="gemonio_styles[title_line_height]" value="<?php echo esc_attr( (string) $s['title_line_height'] ); ?>"></div><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Schreibweise', 'gemonio' ); ?></strong></label><select name="gemonio_styles[title_transform]"><option value="uppercase" <?php selected( $s['title_transform'], 'uppercase' ); ?>><?php esc_html_e( 'VERSALIEN', 'gemonio' ); ?></option><option value="none" <?php selected( $s['title_transform'], 'none' ); ?>><?php esc_html_e( 'Wie eingegeben', 'gemonio' ); ?></option></select></div><?php gemonio_style_number_field( 'h3_size', __( 'Zwischenüberschrift H3', 'gemonio' ), $s['h3_size'], 18, 60 ); ?><?php gemonio_style_weight_field( 'h3_weight', __( 'H3 Gewicht', 'gemonio' ), (int) $s['h3_weight'] ); ?></div></div>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Navigation & Separator-Text', 'gemonio' ); ?></h3><div class="gemonio-style-field gemonio-style-field--wide"><label><strong><?php esc_html_e( 'Navigationsschrift', 'gemonio' ); ?></strong></label><input class="widefat" type="text" name="gemonio_styles[nav_font]" value="<?php echo esc_attr( $s['nav_font'] ); ?>"></div><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'nav_size', __( 'Navigation', 'gemonio' ), $s['nav_size'], 11, 24 ); ?><?php gemonio_style_weight_field( 'nav_weight', __( 'Navigationsgewicht', 'gemonio' ), (int) $s['nav_weight'] ); ?></div><div class="gemonio-style-field gemonio-style-field--wide"><label><strong><?php esc_html_e( 'Separator-Schrift', 'gemonio' ); ?></strong></label><input class="widefat" type="text" name="gemonio_styles[separator_font]" value="<?php echo esc_attr( $s['separator_font'] ); ?>"></div><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'separator_size', __( 'Separator-Text', 'gemonio' ), $s['separator_size'], 16, 72 ); ?><?php gemonio_style_weight_field( 'separator_weight', __( 'Separator-Gewicht', 'gemonio' ), (int) $s['separator_weight'] ); ?><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Stil', 'gemonio' ); ?></strong></label><select name="gemonio_styles[separator_style]"><option value="italic" <?php selected( $s['separator_style'], 'italic' ); ?>><?php esc_html_e( 'Kursiv', 'gemonio' ); ?></option><option value="normal" <?php selected( $s['separator_style'], 'normal' ); ?>><?php esc_html_e( 'Normal', 'gemonio' ); ?></option></select></div></div><div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Lokale Fonts (optional)', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Keine Google-Abfrage: Hinterlege bei Bedarf eigene WOFF2-Dateien. Variable Fonts sind ideal, weil dann alle Gewichte von 300–900 sauber funktionieren.', 'gemonio' ); ?></p><?php gemonio_style_font_file_field( 'local_body_font_url', __( 'Textschrift: Body / Navigation / Separator', 'gemonio' ), $s['local_body_font_url'], __( 'Ohne Datei verwendet Gemonio den eingetragenen Font-Stack.', 'gemonio' ) ); ?><?php gemonio_style_font_file_field( 'local_title_font_url', __( 'Titelschrift: Section-Titel / H3', 'gemonio' ), $s['local_title_font_url'], __( 'Ideal für Oswald oder eine andere Display-Schrift als lokale WOFF2-Datei.', 'gemonio' ) ); ?></div><p class="gemonio-font-note"><?php esc_html_e( 'Performance: Gemonio lädt keine Google Fonts heimlich nach. Ohne lokale Datei bleiben Oswald und Source Sans Pro als SCRN-Referenz im Font-Stack mit sicheren Fallbacks.', 'gemonio' ); ?></p></div>
					<?php elseif ( 'colors' === $tab ) : ?>
						<?php gemonio_color_palette_picker( $s ); ?>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Feinabstimmung', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Jede Palette bleibt vollständig editierbar.', 'gemonio' ); ?></p><div class="gemonio-style-fields gemonio-style-fields--colors"><?php gemonio_style_color_field( 'light_bg', __( 'Helle Section: Hintergrund', 'gemonio' ), $s['light_bg'] ); ?><?php gemonio_style_color_field( 'light_text', __( 'Helle Section: Text', 'gemonio' ), $s['light_text'] ); ?><?php gemonio_style_color_field( 'dark_bg', __( 'Dunkle Section: Hintergrund', 'gemonio' ), $s['dark_bg'] ); ?><?php gemonio_style_color_field( 'dark_text', __( 'Dunkle Section: Text', 'gemonio' ), $s['dark_text'] ); ?><?php gemonio_style_color_field( 'mist_bg', __( 'Soft Grey', 'gemonio' ), $s['mist_bg'] ); ?><?php gemonio_style_color_field( 'heading_color', __( 'Inhaltsüberschriften', 'gemonio' ), $s['heading_color'] ); ?><?php gemonio_style_color_field( 'title_color', __( 'Section-Titel', 'gemonio' ), $s['title_color'] ); ?><?php gemonio_style_color_field( 'nav_bg', __( 'Navigation: Hintergrund', 'gemonio' ), $s['nav_bg'] ); ?><?php gemonio_style_color_field( 'nav_text', __( 'Navigation: Text', 'gemonio' ), $s['nav_text'] ); ?><?php gemonio_style_color_field( 'nav_active_color', __( 'Navigation: Aktiv', 'gemonio' ), $s['nav_active_color'] ); ?><?php gemonio_style_color_field( 'line_color', __( 'Linien / Trenner', 'gemonio' ), $s['line_color'] ); ?><?php gemonio_style_color_field( 'accent_color', __( 'Akzent', 'gemonio' ), $s['accent_color'] ); ?><?php gemonio_style_color_field( 'separator_text_color', __( 'Separator-Text', 'gemonio' ), $s['separator_text_color'] ); ?></div></div>
					<?php elseif ( 'sections' === $tab ) : ?>
						<div class="gemonio-style-fields"><?php gemonio_style_number_field( 'shell_width', __( 'Breite: Wide', 'gemonio' ), $s['shell_width'], 900, 1600 ); ?><?php gemonio_style_number_field( 'content_width', __( 'Breite: Standard', 'gemonio' ), $s['content_width'], 560, 1200 ); ?><?php gemonio_style_number_field( 'section_spacing', __( 'Section-Abstand oben/unten', 'gemonio' ), $s['section_spacing'], 30, 180 ); ?><?php gemonio_style_number_field( 'title_spacing', __( 'Abstand unter Titel', 'gemonio' ), $s['title_spacing'], 16, 100 ); ?><?php gemonio_style_number_field( 'title_rule_gap', __( 'Abstand Titel / Linien', 'gemonio' ), $s['title_rule_gap'], 10, 80 ); ?><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Titelausrichtung', 'gemonio' ); ?></strong></label><select name="gemonio_styles[title_align]"><option value="left" <?php selected( $s['title_align'], 'left' ); ?>><?php esc_html_e( 'Links', 'gemonio' ); ?></option><option value="center" <?php selected( $s['title_align'], 'center' ); ?>><?php esc_html_e( 'Zentriert', 'gemonio' ); ?></option><option value="right" <?php selected( $s['title_align'], 'right' ); ?>><?php esc_html_e( 'Rechts', 'gemonio' ); ?></option></select></div><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Trennlinien', 'gemonio' ); ?></strong></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[title_rule]" value="1" <?php checked( $s['title_rule'] ); ?>> <?php esc_html_e( 'Linien anzeigen', 'gemonio' ); ?></label><select name="gemonio_styles[title_rule_position]"><option value="both" <?php selected( $s['title_rule_position'], 'both' ); ?>><?php esc_html_e( 'Oben und unten', 'gemonio' ); ?></option><option value="top" <?php selected( $s['title_rule_position'], 'top' ); ?>><?php esc_html_e( 'Nur oben', 'gemonio' ); ?></option><option value="bottom" <?php selected( $s['title_rule_position'], 'bottom' ); ?>><?php esc_html_e( 'Nur unten', 'gemonio' ); ?></option></select></div></div>
					<?php elseif ( 'navigation' === $tab ) : ?>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Branding', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Seitentitel oder Logo – direkt im Gemonio-Header, ohne zusätzlichen Customizer-Umweg.', 'gemonio' ); ?></p><div class="gemonio-style-fields"><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Anzeige', 'gemonio' ); ?></strong></label><select name="gemonio_styles[brand_mode]"><option value="auto" <?php selected( $s['brand_mode'], 'auto' ); ?>><?php esc_html_e( 'Automatisch', 'gemonio' ); ?></option><option value="title" <?php selected( $s['brand_mode'], 'title' ); ?>><?php esc_html_e( 'Seitentitel', 'gemonio' ); ?></option><option value="logo" <?php selected( $s['brand_mode'], 'logo' ); ?>><?php esc_html_e( 'Logo', 'gemonio' ); ?></option></select></div><div class="gemonio-style-field gemonio-style-field--wide" data-gemonio-logo-field><label><strong><?php esc_html_e( 'Logo', 'gemonio' ); ?></strong></label><input type="hidden" name="gemonio_styles[brand_logo_id]" value="<?php echo esc_attr( (string) $s['brand_logo_id'] ); ?>" data-gemonio-logo-id><div class="gemonio-brand-logo-preview" data-gemonio-logo-preview><?php if ( $s['brand_logo_id'] ) { echo wp_get_attachment_image( (int) $s['brand_logo_id'], 'medium' ); } ?></div><p><button type="button" class="button" data-gemonio-logo-select><?php esc_html_e( 'Logo wählen', 'gemonio' ); ?></button> <button type="button" class="button-link-delete" data-gemonio-logo-remove <?php echo $s['brand_logo_id'] ? '' : 'hidden'; ?>><?php esc_html_e( 'Entfernen', 'gemonio' ); ?></button></p></div><?php gemonio_style_number_field( 'brand_logo_height', __( 'Logo-Höhe Desktop', 'gemonio' ), $s['brand_logo_height'], 18, 100 ); ?><?php gemonio_style_number_field( 'brand_logo_mobile', __( 'Logo-Höhe Mobile', 'gemonio' ), $s['brand_logo_mobile'], 18, 80 ); ?></div></div><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'header_height', __( 'Navigationshöhe', 'gemonio' ), $s['header_height'], 52, 120 ); ?><?php gemonio_style_number_field( 'nav_gap', __( 'Abstand Menüeinträge', 'gemonio' ), $s['nav_gap'], 8, 60 ); ?><?php gemonio_style_number_field( 'nav_compact_height', __( 'Kompakte Höhe beim Scrollen', 'gemonio' ), $s['nav_compact_height'], 46, 90 ); ?><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Verhalten', 'gemonio' ); ?></strong></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[nav_sticky]" value="1" <?php checked( $s['nav_sticky'] ); ?>> <?php esc_html_e( 'Sticky Navigation', 'gemonio' ); ?></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[nav_blur]" value="1" <?php checked( $s['nav_blur'] ); ?>> <?php esc_html_e( 'Hintergrund weichzeichnen', 'gemonio' ); ?></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[nav_compact]" value="1" <?php checked( $s['nav_compact'] ); ?>> <?php esc_html_e( 'Navigation beim Scrollen leicht verkleinern', 'gemonio' ); ?></label></div></div>
					<?php elseif ( 'motion' === $tab ) : ?>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Scrollgefühl', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Frei einstellbar: 0 ms ist direkt, ca. 420 ms zügig, 700 ms smooth, 1050 ms sehr weich.', 'gemonio' ); ?></p><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'scroll_duration', __( 'Scroll-Dauer', 'gemonio' ), $s['scroll_duration'], 0, 1400, 'ms' ); ?><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Bewegungsverlauf', 'gemonio' ); ?></strong></label><select name="gemonio_styles[scroll_easing]"><option value="natural" <?php selected( $s['scroll_easing'], 'natural' ); ?>><?php esc_html_e( 'Natürlich', 'gemonio' ); ?></option><option value="soft" <?php selected( $s['scroll_easing'], 'soft' ); ?>><?php esc_html_e( 'Soft', 'gemonio' ); ?></option><option value="linear" <?php selected( $s['scroll_easing'], 'linear' ); ?>><?php esc_html_e( 'Linear', 'gemonio' ); ?></option></select></div></div></div><div class="gemonio-style-subsection"><h3><?php esc_html_e( 'One-Page-Komfort', 'gemonio' ); ?></h3><div class="gemonio-style-fields"><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'URL', 'gemonio' ); ?></strong></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[scroll_update_hash]" value="1" <?php checked( $s['scroll_update_hash'] ); ?>> <?php esc_html_e( 'Section-Anker in der URL aktualisieren', 'gemonio' ); ?></label></div><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Nach oben', 'gemonio' ); ?></strong></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[back_to_top]" value="1" <?php checked( $s['back_to_top'] ); ?>> <?php esc_html_e( 'Dezenten Zurück-nach-oben-Button anzeigen', 'gemonio' ); ?></label></div></div></div>
					<?php elseif ( 'buttons' === $tab ) : ?>
						<div class="gemonio-style-fields gemonio-style-fields--colors"><?php gemonio_style_color_field( 'button_bg', __( 'Hintergrund', 'gemonio' ), $s['button_bg'] ); ?><?php gemonio_style_color_field( 'button_text', __( 'Text', 'gemonio' ), $s['button_text'] ); ?></div><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'button_radius', __( 'Eckenradius', 'gemonio' ), $s['button_radius'], 0, 50 ); ?><?php gemonio_style_number_field( 'button_padding_y', __( 'Vertikaler Innenabstand', 'gemonio' ), $s['button_padding_y'], 6, 28 ); ?><?php gemonio_style_number_field( 'button_padding_x', __( 'Horizontaler Innenabstand', 'gemonio' ), $s['button_padding_x'], 10, 50 ); ?><?php gemonio_style_weight_field( 'button_weight', __( 'Schriftgewicht', 'gemonio' ), (int) $s['button_weight'] ); ?></div>
					<?php elseif ( 'media' === $tab ) : ?>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Lightbox', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Unverlinkte Bilder innerhalb von Sections öffnen sich gross. Kein Plugin, keine externe Library.', 'gemonio' ); ?></p><div class="gemonio-style-fields"><div class="gemonio-style-field"><label><strong><?php esc_html_e( 'Aktivierung', 'gemonio' ); ?></strong></label><label class="gemonio-switch-row"><input type="checkbox" name="gemonio_styles[lightbox_enabled]" value="1" <?php checked( $s['lightbox_enabled'] ); ?>> <?php esc_html_e( 'Gemonio Lightbox aktivieren', 'gemonio' ); ?></label></div><?php gemonio_style_color_field( 'lightbox_overlay', __( 'Overlay-Farbe', 'gemonio' ), $s['lightbox_overlay'] ); ?><?php gemonio_style_number_field( 'lightbox_opacity', __( 'Overlay-Deckkraft', 'gemonio' ), $s['lightbox_opacity'], 50, 100, '%' ); ?></div></div>
					<?php elseif ( 'parallax' === $tab ) : ?>
						<p class="gemonio-style-lead"><?php esc_html_e( 'Der Separator bleibt das visuelle Herzstück zwischen den Sections. Stark in der Wirkung, knapp in den Optionen.', 'gemonio' ); ?></p><div class="gemonio-style-fields"><?php gemonio_style_number_field( 'parallax_compact', __( 'Kompakt', 'gemonio' ), $s['parallax_compact'], 120, 500 ); ?><?php gemonio_style_number_field( 'parallax_normal', __( 'Normal', 'gemonio' ), $s['parallax_normal'], 180, 800 ); ?><?php gemonio_style_number_field( 'parallax_large', __( 'Gross', 'gemonio' ), $s['parallax_large'], 260, 1000 ); ?><?php gemonio_style_number_field( 'parallax_overlay', __( 'Bild-Overlay', 'gemonio' ), $s['parallax_overlay'], 0, 70, '%' ); ?></div>
					<?php else : ?>
						<div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Additional CSS', 'gemonio' ); ?></h3><p><?php esc_html_e( 'Für gezielte Overrides. Dieses CSS wird über WordPress themegebunden gespeichert und bleibt bei Gemonio-Updates erhalten.', 'gemonio' ); ?></p><textarea id="gemonio-additional-css" name="gemonio_custom_css" rows="20" class="widefat code" data-gemonio-code-editor><?php echo esc_textarea( wp_get_custom_css() ); ?></textarea><p class="description"><?php esc_html_e( 'Wird nach den normalen Theme-Styles ausgegeben. Der SCRN-Classic-Reset löscht dieses CSS bewusst nicht.', 'gemonio' ); ?></p></div><div class="gemonio-style-subsection"><h3><?php esc_html_e( 'Nützliche CSS-Variablen', 'gemonio' ); ?></h3><div class="gemonio-css-vars"><code>--gemonio-accent</code><code>--gemonio-title-color</code><code>--gemonio-line</code><code>--gemonio-content</code><code>--gemonio-shell</code><code>--gemonio-section-y</code><code>--gemonio-header-height</code></div></div>
					<?php endif; ?>
				</div>
				<div class="gemonio-style-actions"><div><button class="button button-primary button-large" type="submit"><?php esc_html_e( 'Styles speichern', 'gemonio' ); ?></button><?php if ( 'advanced' !== $tab ) : ?><button class="button" type="submit" name="gemonio_reset_group" value="1" onclick="return confirm('<?php echo esc_js( __( 'Diesen Style-Bereich auf SCRN Classic zurücksetzen?', 'gemonio' ) ); ?>');"><?php esc_html_e( 'Bereich zurücksetzen', 'gemonio' ); ?></button><?php else : ?><button class="button-link-delete" type="submit" name="gemonio_clear_custom_css" value="1" onclick="return confirm('<?php echo esc_js( __( 'Additional CSS wirklich leeren?', 'gemonio' ) ); ?>');"><?php esc_html_e( 'Custom CSS leeren', 'gemonio' ); ?></button><?php endif; ?></div><button class="button-link-delete" type="submit" name="gemonio_reset_all" value="1" onclick="return confirm('<?php echo esc_js( __( 'Wirklich ALLE Styles auf SCRN Classic zurücksetzen?', 'gemonio' ) ); ?>');"><?php esc_html_e( 'Alle Styles zurücksetzen', 'gemonio' ); ?></button></div>
			</form>

			<aside class="gemonio-style-preview-wrap" data-gemonio-preview-wrap>
				<div class="gemonio-style-preview-head">
					<div class="gemonio-style-preview-head__copy"><h2><?php esc_html_e( 'Live-Vorschau', 'gemonio' ); ?></h2><p><?php esc_html_e( 'Kompakt als Hilfe – bei Bedarf gross.', 'gemonio' ); ?></p></div>
					<div class="gemonio-style-preview-tools" aria-label="<?php esc_attr_e( 'Vorschau steuern', 'gemonio' ); ?>">
						<div class="gemonio-preview-device" role="group" aria-label="<?php esc_attr_e( 'Vorschaugerät', 'gemonio' ); ?>">
							<button class="gemonio-preview-tool is-active" type="button" data-gemonio-preview-device="desktop" aria-pressed="true" title="<?php esc_attr_e( 'Desktop-Vorschau', 'gemonio' ); ?>"><span class="dashicons dashicons-desktop"></span></button>
							<button class="gemonio-preview-tool" type="button" data-gemonio-preview-device="mobile" aria-pressed="false" title="<?php esc_attr_e( 'Mobile Vorschau', 'gemonio' ); ?>"><span class="dashicons dashicons-smartphone"></span></button>
						</div>
						<button class="gemonio-preview-tool" type="button" data-gemonio-preview-expand aria-pressed="false" title="<?php esc_attr_e( 'Vorschau vergrössern', 'gemonio' ); ?>"><span class="dashicons dashicons-editor-expand"></span></button>
						<button class="gemonio-preview-tool" type="button" data-gemonio-preview-collapse aria-pressed="false" title="<?php esc_attr_e( 'Vorschau einklappen', 'gemonio' ); ?>"><span class="dashicons dashicons-arrow-right-alt2"></span></button>
					</div>
				</div>
				<div class="gemonio-style-preview-body" data-gemonio-preview-body>
					<?php gemonio_styles_preview( $s ); ?>
				</div>
				<button class="gemonio-preview-reopen" type="button" data-gemonio-preview-reopen title="<?php esc_attr_e( 'Vorschau öffnen', 'gemonio' ); ?>"><span class="dashicons dashicons-visibility"></span><span class="screen-reader-text"><?php esc_html_e( 'Vorschau öffnen', 'gemonio' ); ?></span></button>
			</aside>
		</div>
	</div>
	<?php
}

