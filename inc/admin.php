<?php
/**
 * Minimal administration for Gemonio Sections.
 *
 * @package Gemonio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gemonio_admin_menu(): void {
	add_menu_page(
		__( 'Gemonio', 'gemonio' ),
		__( 'Gemonio', 'gemonio' ),
		'edit_pages',
		'gemonio-theme',
		'gemonio_admin_overview',
		'dashicons-layout',
		58
	);

	add_submenu_page(
		'gemonio-theme',
		__( 'Overview', 'gemonio' ),
		__( 'Overview', 'gemonio' ),
		'edit_pages',
		'gemonio-theme',
		'gemonio_admin_overview'
	);

	add_submenu_page(
		'gemonio-theme',
		__( 'Section order', 'gemonio' ),
		__( 'Section order', 'gemonio' ),
		'edit_pages',
		'gemonio-section-order',
		'gemonio_section_order_page'
	);

	add_submenu_page(
		'gemonio-theme',
		__( 'Migration', 'gemonio' ),
		__( 'Migration', 'gemonio' ),
		'manage_options',
		'gemonio-migration',
		'gemonio_migration_page'
	);
}
add_action( 'admin_menu', 'gemonio_admin_menu', 9 );

function gemonio_admin_overview(): void {
	$count = wp_count_posts( 'gemonio_section' );
	$total = $count && isset( $count->publish ) ? (int) $count->publish : 0;
	?>
	<div class="wrap gemonio-admin-wrap">
		<h1><?php esc_html_e( 'Gemonio Theme', 'gemonio' ); ?></h1>
		<p class="description"><?php esc_html_e( 'One page. Clear sections. No builder circus.', 'gemonio' ); ?></p>

		<div class="gemonio-admin-card">
			<h2><?php esc_html_e( 'Sections', 'gemonio' ); ?></h2>
			<p><?php echo esc_html( sprintf( _n( '%d published section', '%d published sections', $total, 'gemonio' ), $total ) ); ?></p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=gemonio_section' ) ); ?>"><?php esc_html_e( 'Add section', 'gemonio' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=gemonio-section-order' ) ); ?>"><?php esc_html_e( 'Change order', 'gemonio' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=gemonio-styles' ) ); ?>"><?php esc_html_e( 'Styles', 'gemonio' ); ?></a>
				<?php if ( current_user_can( 'manage_options' ) ) : ?><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=gemonio-migration' ) ); ?>"><?php esc_html_e( 'SCRN migration', 'gemonio' ); ?></a><?php endif; ?>
			</p>
		</div>

		<div class="gemonio-admin-card">
			<h2><?php esc_html_e( 'How it works', 'gemonio' ); ?></h2>
			<p><?php esc_html_e( 'Each Gemonio Section is one part of the one-page site. Write its content with the normal WordPress block editor, choose one of the few section styles and optionally place an image or parallax separator after it. Global typography, colours, spacing, navigation, buttons and separators live centrally under Gemonio → Styles.', 'gemonio' ); ?></p>
			<p><?php esc_html_e( 'If no WordPress menu is assigned to the Primary navigation location, Gemonio builds the navigation automatically from your sections.', 'gemonio' ); ?></p>
		</div>
	</div>
	<?php
}

function gemonio_add_section_meta_boxes(): void {
	add_meta_box(
		'gemonio-section-settings',
		__( 'Section settings', 'gemonio' ),
		'gemonio_section_settings_box',
		'gemonio_section',
		'side',
		'high'
	);

	add_meta_box(
		'gemonio-separator-settings',
		__( 'Separator after this section', 'gemonio' ),
		'gemonio_separator_settings_box',
		'gemonio_section',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'gemonio_add_section_meta_boxes' );

function gemonio_section_settings_box( WP_Post $post ): void {
	wp_nonce_field( 'gemonio_save_section', 'gemonio_section_nonce' );

	$style     = (string) get_post_meta( $post->ID, '_gemonio_style', true );
	$style     = in_array( $style, array( 'light', 'dark', 'mist' ), true ) ? $style : 'light';
	$width     = (string) get_post_meta( $post->ID, '_gemonio_width', true );
	$width     = in_array( $width, array( 'standard', 'wide' ), true ) ? $width : 'standard';
	$in_nav    = gemonio_section_is_in_navigation( $post->ID );
	$nav_label = (string) get_post_meta( $post->ID, '_gemonio_nav_label', true );
	$anchor    = (string) get_post_meta( $post->ID, '_gemonio_anchor', true );
	$show_title_meta = get_post_meta( $post->ID, '_gemonio_show_title', true );
	$show_title = '' === $show_title_meta || '1' === $show_title_meta;
	$subtitle   = (string) get_post_meta( $post->ID, '_gemonio_subtitle', true );
	?>
	<p>
		<label for="gemonio-style"><strong><?php esc_html_e( 'Style', 'gemonio' ); ?></strong></label><br>
		<select id="gemonio-style" name="gemonio_style" class="widefat">
			<option value="light" <?php selected( $style, 'light' ); ?>><?php esc_html_e( 'Light', 'gemonio' ); ?></option>
			<option value="dark" <?php selected( $style, 'dark' ); ?>><?php esc_html_e( 'Dark', 'gemonio' ); ?></option>
			<option value="mist" <?php selected( $style, 'mist' ); ?>><?php esc_html_e( 'Soft grey', 'gemonio' ); ?></option>
		</select>
	</p>
	<p>
		<label for="gemonio-width"><strong><?php esc_html_e( 'Content width', 'gemonio' ); ?></strong></label><br>
		<select id="gemonio-width" name="gemonio_width" class="widefat">
			<option value="standard" <?php selected( $width, 'standard' ); ?>><?php esc_html_e( 'Standard', 'gemonio' ); ?></option>
			<option value="wide" <?php selected( $width, 'wide' ); ?>><?php esc_html_e( 'Wide', 'gemonio' ); ?></option>
		</select>
	</p>
	<p>
		<label><input type="checkbox" name="gemonio_show_title" value="1" <?php checked( $show_title ); ?>> <?php esc_html_e( 'Show section title', 'gemonio' ); ?></label><br>
		<small><?php esc_html_e( 'Uses the section title automatically. Recommended for normal content sections.', 'gemonio' ); ?></small>
	</p>
	<p>
		<label for="gemonio-subtitle"><strong><?php esc_html_e( 'Section subtitle', 'gemonio' ); ?></strong></label><br>
		<input id="gemonio-subtitle" type="text" class="widefat" name="gemonio_subtitle" value="<?php echo esc_attr( $subtitle ); ?>">
		<small><?php esc_html_e( 'Optional short claim below the title.', 'gemonio' ); ?></small>
	</p>
	<hr>
	<p>
		<label><input type="checkbox" name="gemonio_in_nav" value="1" <?php checked( $in_nav ); ?>> <?php esc_html_e( 'Show in navigation', 'gemonio' ); ?></label>
	</p>
	<p>
		<label for="gemonio-nav-label"><strong><?php esc_html_e( 'Navigation label', 'gemonio' ); ?></strong></label><br>
		<input id="gemonio-nav-label" type="text" class="widefat" name="gemonio_nav_label" value="<?php echo esc_attr( $nav_label ); ?>" placeholder="<?php echo esc_attr( get_the_title( $post ) ); ?>">
	</p>
	<p>
		<label for="gemonio-anchor"><strong><?php esc_html_e( 'Anchor', 'gemonio' ); ?></strong></label><br>
		<input id="gemonio-anchor" type="text" class="widefat" name="gemonio_anchor" value="<?php echo esc_attr( $anchor ); ?>" placeholder="<?php echo esc_attr( sanitize_title( get_the_title( $post ) ) ); ?>">
		<small><?php esc_html_e( 'Optional. Leave empty to use the section slug.', 'gemonio' ); ?></small>
	</p>
	<?php
}

function gemonio_separator_settings_box( WP_Post $post ): void {
	$type     = (string) get_post_meta( $post->ID, '_gemonio_separator_type', true );
	$type     = in_array( $type, array( 'none', 'image', 'parallax' ), true ) ? $type : 'none';
	$image_id = absint( get_post_meta( $post->ID, '_gemonio_separator_image_id', true ) );
	$height   = (string) get_post_meta( $post->ID, '_gemonio_separator_height', true );
	$height   = in_array( $height, array( 'compact', 'normal', 'large' ), true ) ? $height : 'normal';
	$text     = (string) get_post_meta( $post->ID, '_gemonio_separator_text', true );
	?>
	<div class="gemonio-separator-fields">
		<p>
			<label for="gemonio-separator-type"><strong><?php esc_html_e( 'Separator', 'gemonio' ); ?></strong></label><br>
			<select id="gemonio-separator-type" name="gemonio_separator_type">
				<option value="none" <?php selected( $type, 'none' ); ?>><?php esc_html_e( 'None', 'gemonio' ); ?></option>
				<option value="image" <?php selected( $type, 'image' ); ?>><?php esc_html_e( 'Image', 'gemonio' ); ?></option>
				<option value="parallax" <?php selected( $type, 'parallax' ); ?>><?php esc_html_e( 'Parallax', 'gemonio' ); ?></option>
			</select>
		</p>

		<div class="gemonio-separator-media" data-gemonio-media-field>
			<input type="hidden" name="gemonio_separator_image_id" value="<?php echo esc_attr( (string) $image_id ); ?>" data-gemonio-media-id>
			<div class="gemonio-separator-preview" data-gemonio-media-preview>
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'medium' ); ?>
				<?php endif; ?>
			</div>
			<p>
				<button type="button" class="button" data-gemonio-media-select><?php esc_html_e( 'Choose image', 'gemonio' ); ?></button>
				<button type="button" class="button-link-delete" data-gemonio-media-remove <?php echo $image_id ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove', 'gemonio' ); ?></button>
			</p>
		</div>

		<p>
			<label for="gemonio-separator-height"><strong><?php esc_html_e( 'Height', 'gemonio' ); ?></strong></label><br>
			<select id="gemonio-separator-height" name="gemonio_separator_height">
				<option value="compact" <?php selected( $height, 'compact' ); ?>><?php esc_html_e( 'Compact', 'gemonio' ); ?></option>
				<option value="normal" <?php selected( $height, 'normal' ); ?>><?php esc_html_e( 'Normal', 'gemonio' ); ?></option>
				<option value="large" <?php selected( $height, 'large' ); ?>><?php esc_html_e( 'Large', 'gemonio' ); ?></option>
			</select>
		</p>

		<p>
			<label for="gemonio-separator-text"><strong><?php esc_html_e( 'Text on separator', 'gemonio' ); ?></strong></label><br>
			<input id="gemonio-separator-text" type="text" class="widefat" name="gemonio_separator_text" value="<?php echo esc_attr( $text ); ?>">
			<small><?php esc_html_e( 'Optional.', 'gemonio' ); ?></small>
		</p>
	</div>
	<?php
}

function gemonio_save_section_meta( int $post_id ): void {
	if ( ! isset( $_POST['gemonio_section_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gemonio_section_nonce'] ) ), 'gemonio_save_section' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'gemonio_section' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$style = isset( $_POST['gemonio_style'] ) ? sanitize_key( wp_unslash( $_POST['gemonio_style'] ) ) : 'light';
	$style = in_array( $style, array( 'light', 'dark', 'mist' ), true ) ? $style : 'light';
	update_post_meta( $post_id, '_gemonio_style', $style );

	$width = isset( $_POST['gemonio_width'] ) ? sanitize_key( wp_unslash( $_POST['gemonio_width'] ) ) : 'standard';
	$width = in_array( $width, array( 'standard', 'wide' ), true ) ? $width : 'standard';
	update_post_meta( $post_id, '_gemonio_width', $width );

	update_post_meta( $post_id, '_gemonio_show_title', isset( $_POST['gemonio_show_title'] ) ? '1' : '0' );
	$subtitle = isset( $_POST['gemonio_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['gemonio_subtitle'] ) ) : '';
	update_post_meta( $post_id, '_gemonio_subtitle', $subtitle );
	update_post_meta( $post_id, '_gemonio_in_nav', isset( $_POST['gemonio_in_nav'] ) ? '1' : '0' );

	$nav_label = isset( $_POST['gemonio_nav_label'] ) ? sanitize_text_field( wp_unslash( $_POST['gemonio_nav_label'] ) ) : '';
	update_post_meta( $post_id, '_gemonio_nav_label', $nav_label );

	$anchor = isset( $_POST['gemonio_anchor'] ) ? sanitize_title( wp_unslash( $_POST['gemonio_anchor'] ) ) : '';
	update_post_meta( $post_id, '_gemonio_anchor', $anchor );

	$type = isset( $_POST['gemonio_separator_type'] ) ? sanitize_key( wp_unslash( $_POST['gemonio_separator_type'] ) ) : 'none';
	$type = in_array( $type, array( 'none', 'image', 'parallax' ), true ) ? $type : 'none';
	update_post_meta( $post_id, '_gemonio_separator_type', $type );

	$image_id = isset( $_POST['gemonio_separator_image_id'] ) ? absint( $_POST['gemonio_separator_image_id'] ) : 0;
	update_post_meta( $post_id, '_gemonio_separator_image_id', $image_id );

	$height = isset( $_POST['gemonio_separator_height'] ) ? sanitize_key( wp_unslash( $_POST['gemonio_separator_height'] ) ) : 'normal';
	$height = in_array( $height, array( 'compact', 'normal', 'large' ), true ) ? $height : 'normal';
	update_post_meta( $post_id, '_gemonio_separator_height', $height );

	$text = isset( $_POST['gemonio_separator_text'] ) ? sanitize_text_field( wp_unslash( $_POST['gemonio_separator_text'] ) ) : '';
	update_post_meta( $post_id, '_gemonio_separator_text', $text );
}
add_action( 'save_post_gemonio_section', 'gemonio_save_section_meta' );

function gemonio_admin_assets( string $hook ): void {
	$screen = get_current_screen();
	$theme  = wp_get_theme();
	$ver    = $theme->get( 'Version' );

	if ( $screen && 'gemonio_section' === $screen->post_type && in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_script(
			'gemonio-admin-section',
			get_template_directory_uri() . '/assets/js/admin-section.js',
			array( 'jquery' ),
			$ver,
			true
		);
	}

	if ( 'gemonio_page_gemonio-section-order' === $hook ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script(
			'gemonio-admin-order',
			get_template_directory_uri() . '/assets/js/admin-order.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			$ver,
			true
		);
		wp_localize_script(
			'gemonio-admin-order',
			'gemonioOrder',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'gemonio_save_section_order' ),
			)
		);
	}

	if ( false !== strpos( $hook, 'gemonio' ) || ( $screen && 'gemonio_section' === $screen->post_type ) ) {
		wp_enqueue_style(
			'gemonio-admin',
			get_template_directory_uri() . '/assets/css/admin.css',
			array(),
			$ver
		);
	}
}
add_action( 'admin_enqueue_scripts', 'gemonio_admin_assets' );

function gemonio_section_order_page(): void {
	$sections = get_posts(
		array(
			'post_type'      => 'gemonio_section',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
		)
	);
	?>
	<div class="wrap gemonio-admin-wrap">
		<h1><?php esc_html_e( 'Section order', 'gemonio' ); ?></h1>
		<p><?php esc_html_e( 'Drag the sections into the order in which they should appear on the one-page site.', 'gemonio' ); ?></p>

		<?php if ( empty( $sections ) ) : ?>
			<div class="gemonio-admin-card">
				<p><?php esc_html_e( 'No sections yet.', 'gemonio' ); ?></p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=gemonio_section' ) ); ?>"><?php esc_html_e( 'Add section', 'gemonio' ); ?></a>
			</div>
		<?php else : ?>
			<ul class="gemonio-order-list" data-gemonio-order-list>
				<?php foreach ( $sections as $section ) : ?>
					<li data-id="<?php echo esc_attr( (string) $section->ID ); ?>">
						<span class="dashicons dashicons-menu" aria-hidden="true"></span>
						<strong><?php echo esc_html( get_the_title( $section ) ?: __( '(Untitled)', 'gemonio' ) ); ?></strong>
						<span class="gemonio-order-status"><?php echo esc_html( get_post_status_object( $section->post_status )->label ); ?></span>
						<a href="<?php echo esc_url( get_edit_post_link( $section->ID ) ); ?>"><?php esc_html_e( 'Edit', 'gemonio' ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="description" data-gemonio-order-message aria-live="polite"></p>
		<?php endif; ?>
	</div>
	<?php
}

function gemonio_ajax_save_section_order(): void {
	check_ajax_referer( 'gemonio_save_section_order', 'nonce' );

	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'gemonio' ) ), 403 );
	}

	$order = isset( $_POST['order'] ) && is_array( $_POST['order'] ) ? array_map( 'absint', wp_unslash( $_POST['order'] ) ) : array();

	foreach ( $order as $position => $post_id ) {
		if ( 'gemonio_section' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'         => $post_id,
				'menu_order' => (int) $position,
			)
		);
	}

	wp_send_json_success( array( 'message' => __( 'Order saved.', 'gemonio' ) ) );
}
add_action( 'wp_ajax_gemonio_save_section_order', 'gemonio_ajax_save_section_order' );

function gemonio_section_columns( array $columns ): array {
	$columns['gemonio_style'] = __( 'Style', 'gemonio' );
	$columns['gemonio_nav']   = __( 'Navigation', 'gemonio' );
	$columns['gemonio_order'] = __( 'Order', 'gemonio' );
	return $columns;
}
add_filter( 'manage_gemonio_section_posts_columns', 'gemonio_section_columns' );

function gemonio_section_column_content( string $column, int $post_id ): void {
	if ( 'gemonio_style' === $column ) {
		$style = (string) get_post_meta( $post_id, '_gemonio_style', true );
		echo esc_html( $style ?: 'light' );
	}

	if ( 'gemonio_nav' === $column ) {
		echo gemonio_section_is_in_navigation( $post_id ) ? esc_html__( 'Yes', 'gemonio' ) : esc_html__( 'No', 'gemonio' );
	}

	if ( 'gemonio_order' === $column ) {
		echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
	}
}
add_action( 'manage_gemonio_section_posts_custom_column', 'gemonio_section_column_content', 10, 2 );
