<?php
/**
 * One-page section system.
 *
 * @package Gemonio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the native section content type.
 */
function gemonio_register_section_type(): void {
	$labels = array(
		'name'               => __( 'Sections', 'gemonio' ),
		'singular_name'      => __( 'Section', 'gemonio' ),
		'add_new'            => __( 'Add section', 'gemonio' ),
		'add_new_item'       => __( 'Add section', 'gemonio' ),
		'edit_item'          => __( 'Edit section', 'gemonio' ),
		'new_item'           => __( 'New section', 'gemonio' ),
		'view_item'          => __( 'View section', 'gemonio' ),
		'search_items'       => __( 'Search sections', 'gemonio' ),
		'not_found'          => __( 'No sections found.', 'gemonio' ),
		'not_found_in_trash' => __( 'No sections found in Trash.', 'gemonio' ),
		'menu_name'          => __( 'Sections', 'gemonio' ),
	);

	register_post_type(
		'gemonio_section',
		array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'gemonio-theme',
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-layout',
			'supports'           => array( 'title', 'editor', 'revisions', 'page-attributes' ),
			'map_meta_cap'       => true,
		)
	);
}
add_action( 'init', 'gemonio_register_section_type' );

/**
 * Return all published one-page sections in their chosen order.
 *
 * @return WP_Post[]
 */
function gemonio_get_sections(): array {
	$sections = get_posts(
		array(
			'post_type'      => 'gemonio_section',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'ID'         => 'ASC',
			),
		)
	);

	return is_array( $sections ) ? $sections : array();
}

/**
 * Get a stable section anchor.
 */
function gemonio_get_section_anchor( WP_Post $section ): string {
	$anchor = sanitize_title( (string) get_post_meta( $section->ID, '_gemonio_anchor', true ) );

	if ( '' === $anchor ) {
		$anchor = $section->post_name ? sanitize_title( $section->post_name ) : 'section-' . $section->ID;
	}

	return $anchor;
}

/**
 * Determine whether a section is part of the generated one-page navigation.
 */
function gemonio_section_is_in_navigation( int $section_id ): bool {
	$value = get_post_meta( $section_id, '_gemonio_in_nav', true );

	// New sections are in the navigation unless explicitly disabled.
	return '' === $value || '1' === $value;
}

/**
 * Automatic menu fallback generated from sections.
 *
 * @param array|stdClass $args wp_nav_menu arguments.
 */
function gemonio_section_navigation( $args = array() ): void {
	$sections = gemonio_get_sections();
	$items    = array();

	foreach ( $sections as $section ) {
		if ( ! gemonio_section_is_in_navigation( $section->ID ) ) {
			continue;
		}

		$anchor = gemonio_get_section_anchor( $section );
		$label  = trim( (string) get_post_meta( $section->ID, '_gemonio_nav_label', true ) );
		$label  = '' !== $label ? $label : get_the_title( $section );
		$url    = is_front_page() ? '#' . $anchor : home_url( '/#' . $anchor );

		$items[] = sprintf(
			'<li class="menu-item"><a href="%1$s" data-gemonio-section-link="%2$s">%3$s</a></li>',
			esc_url( $url ),
			esc_attr( $anchor ),
			esc_html( $label )
		);
	}

	if ( empty( $items ) ) {
		return;
	}

	$menu_id    = is_object( $args ) && isset( $args->menu_id ) ? $args->menu_id : 'primary-menu';
	$menu_class = is_object( $args ) && isset( $args->menu_class ) ? $args->menu_class : 'primary-menu';

	printf(
		'<ul id="%1$s" class="%2$s">%3$s</ul>',
		esc_attr( $menu_id ),
		esc_attr( $menu_class ),
		implode( '', $items ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Items are escaped above.
	);
}


/**
 * Add section metadata to links of a real WordPress primary menu.
 * This keeps active-section highlighting working for both the generated
 * fallback navigation and the migration-created WordPress menu.
 */
function gemonio_primary_menu_section_attributes( array $atts, WP_Post $menu_item, $args ): array {
	if ( ! is_object( $args ) || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $atts;
	}

	$url      = isset( $atts['href'] ) ? (string) $atts['href'] : '';
	$fragment = wp_parse_url( $url, PHP_URL_FRAGMENT );
	if ( ! is_string( $fragment ) || '' === $fragment ) {
		return $atts;
	}

	$fragment = sanitize_title( $fragment );
	static $anchors = null;
	if ( null === $anchors ) {
		$anchors = array();
		foreach ( gemonio_get_sections() as $section ) {
			if ( gemonio_section_is_in_navigation( $section->ID ) ) {
				$anchors[ gemonio_get_section_anchor( $section ) ] = true;
			}
		}
	}

	if ( isset( $anchors[ $fragment ] ) ) {
		$atts['data-gemonio-section-link'] = $fragment;
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'gemonio_primary_menu_section_attributes', 10, 3 );

/**
 * Whether the automatic section title is visible.
 */
function gemonio_section_shows_title( int $section_id ): bool {
	$value = get_post_meta( $section_id, '_gemonio_show_title', true );
	return '' === $value || '1' === $value;
}

/**
 * Render a single one-page section and its optional separator.
 */
function gemonio_render_section( WP_Post $section ): void {
	$style = (string) get_post_meta( $section->ID, '_gemonio_style', true );
	$style = in_array( $style, array( 'light', 'dark', 'mist' ), true ) ? $style : 'light';

	$width = (string) get_post_meta( $section->ID, '_gemonio_width', true );
	$width = in_array( $width, array( 'standard', 'wide' ), true ) ? $width : 'standard';

	$anchor  = gemonio_get_section_anchor( $section );
	$classes = array(
		'gemonio-onepage-section',
		'gemonio-onepage-section--' . $style,
		'gemonio-onepage-section--' . $width,
	);

	?>
	<section id="<?php echo esc_attr( $anchor ); ?>" <?php post_class( $classes, $section->ID ); ?> data-gemonio-section="<?php echo esc_attr( $anchor ); ?>">
		<div class="gemonio-section__inner">
			<?php if ( gemonio_section_shows_title( $section->ID ) && '' !== trim( get_the_title( $section ) ) ) : ?>
				<?php $subtitle = trim( (string) get_post_meta( $section->ID, '_gemonio_subtitle', true ) ); ?>
				<header class="gemonio-section-title">
					<h2><?php echo esc_html( get_the_title( $section ) ); ?></h2>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="gemonio-section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<div class="gemonio-section-content">
				<?php echo apply_filters( 'the_content', $section->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</section>
	<?php
	gemonio_render_separator( $section );
}

/**
 * Render a section separator.
 */
function gemonio_render_separator( WP_Post $section ): void {
	$type = (string) get_post_meta( $section->ID, '_gemonio_separator_type', true );

	if ( ! in_array( $type, array( 'image', 'parallax' ), true ) ) {
		return;
	}

	$image_id = absint( get_post_meta( $section->ID, '_gemonio_separator_image_id', true ) );
	$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

	if ( ! $image ) {
		return;
	}

	$height = (string) get_post_meta( $section->ID, '_gemonio_separator_height', true );
	$height = in_array( $height, array( 'compact', 'normal', 'large' ), true ) ? $height : 'normal';
	$text   = trim( (string) get_post_meta( $section->ID, '_gemonio_separator_text', true ) );

	$classes = array(
		'gemonio-separator',
		'gemonio-separator--' . $type,
		'gemonio-separator--' . $height,
	);
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="background-image:url('<?php echo esc_url( $image ); ?>')" aria-hidden="<?php echo '' === $text ? 'true' : 'false'; ?>">
		<?php if ( '' !== $text ) : ?>
			<div class="gemonio-separator__inner"><p><?php echo esc_html( $text ); ?></p></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Keep section list ordered like the front end.
 */
function gemonio_order_section_admin_list( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || 'gemonio_section' !== $query->get( 'post_type' ) ) {
		return;
	}

	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'gemonio_order_section_admin_list' );
