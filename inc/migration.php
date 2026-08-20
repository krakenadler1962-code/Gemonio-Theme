<?php
/**
 * SCRN migration helper.
 *
 * @package Gemonio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a migration transient key.
 */
function gemonio_migration_transient_key( string $token ): string {
	return 'gemonio_migration_' . preg_replace( '/[^a-zA-Z0-9_-]/', '', $token );
}

/**
 * Move one uploaded migration file into a private-ish working directory below uploads.
 *
 * @return string|WP_Error
 */
function gemonio_migration_store_upload( string $field, array $allowed_extensions ) {
	if ( empty( $_FILES[ $field ]['tmp_name'] ) || ! is_uploaded_file( $_FILES[ $field ]['tmp_name'] ) ) {
		return new WP_Error( 'missing_file', __( 'A required migration file is missing.', 'gemonio' ) );
	}

	$name = isset( $_FILES[ $field ]['name'] ) ? sanitize_file_name( wp_unslash( $_FILES[ $field ]['name'] ) ) : '';
	$ext  = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );

	if ( ! in_array( $ext, $allowed_extensions, true ) ) {
		return new WP_Error( 'bad_extension', __( 'The uploaded migration file has an unexpected file type.', 'gemonio' ) );
	}

	$dir = trailingslashit( get_temp_dir() ) . 'gemonio-migration';
	if ( ! wp_mkdir_p( $dir ) ) {
		return new WP_Error( 'mkdir_failed', __( 'Gemonio could not create the migration working directory.', 'gemonio' ) );
	}

	$filename = wp_unique_filename( $dir, $name ?: 'scrn-export.' . $ext );
	$target   = trailingslashit( $dir ) . $filename;

	if ( ! move_uploaded_file( $_FILES[ $field ]['tmp_name'], $target ) ) {
		return new WP_Error( 'move_failed', __( 'Gemonio could not store the uploaded migration file.', 'gemonio' ) );
	}

	return $target;
}

/**
 * Read one XML tag from a WXR fragment without requiring a PHP XML extension.
 */
function gemonio_wxr_tag_value( string $fragment, string $tag ): string {
	$pattern = '~<' . preg_quote( $tag, '~' ) . '(?:\\s[^>]*)?>(.*?)</' . preg_quote( $tag, '~' ) . '>~s';
	if ( ! preg_match( $pattern, $fragment, $matches ) ) {
		return '';
	}

	$value = trim( (string) $matches[1] );
	if ( 0 === strpos( $value, '<![CDATA[' ) && substr( $value, -3 ) === ']]>' ) {
		return substr( $value, 9, -3 );
	}

	return html_entity_decode( $value, ENT_QUOTES | ENT_XML1, 'UTF-8' );
}

/**
 * Parse a WordPress WXR export and extract the bits SCRN used for its one-page structure.
 * The parser intentionally uses the stable WXR tag structure and does not depend on
 * SimpleXML/DOM, so it also works on lean PHP installations.
 *
 * @return array|WP_Error
 */
function gemonio_scrn_parse_wxr( string $path ) {
	$raw = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $raw || '' === trim( $raw ) ) {
		return new WP_Error( 'invalid_wxr', __( 'The WordPress XML export could not be read.', 'gemonio' ) );
	}

	if ( false === strpos( $raw, '<rss' ) || false === strpos( $raw, '<channel>' ) || false === strpos( $raw, '<wp:wxr_version>' ) ) {
		return new WP_Error( 'invalid_wxr', __( 'This does not look like a WordPress WXR export.', 'gemonio' ) );
	}

	$channel_head = $raw;
	$item_pos     = strpos( $raw, '<item>' );
	if ( false !== $item_pos ) {
		$channel_head = substr( $raw, 0, $item_pos );
	}

	if ( ! preg_match_all( '~<item>(.*?)</item>~s', $raw, $item_matches ) ) {
		return new WP_Error( 'empty_wxr', __( 'The WordPress export contains no importable items.', 'gemonio' ) );
	}

	$pages      = array();
	$menu_items = array();
	$counts     = array();

	foreach ( $item_matches[1] as $item ) {
		$post_type = gemonio_wxr_tag_value( $item, 'wp:post_type' );
		$counts[ $post_type ] = isset( $counts[ $post_type ] ) ? $counts[ $post_type ] + 1 : 1;

		$meta = array();
		if ( preg_match_all( '~<wp:postmeta>(.*?)</wp:postmeta>~s', $item, $meta_matches ) ) {
			foreach ( $meta_matches[1] as $postmeta ) {
				$key   = gemonio_wxr_tag_value( $postmeta, 'wp:meta_key' );
				$value = gemonio_wxr_tag_value( $postmeta, 'wp:meta_value' );
				if ( '' !== $key ) {
					// WXR may contain duplicate historical meta rows. The latest value wins.
					$meta[ $key ] = $value;
				}
			}
		}

		if ( 'page' === $post_type ) {
			$post_id = (int) gemonio_wxr_tag_value( $item, 'wp:post_id' );
			$pages[ $post_id ] = array(
				'id'             => $post_id,
				'title'          => trim( gemonio_wxr_tag_value( $item, 'title' ) ),
				'slug'           => gemonio_wxr_tag_value( $item, 'wp:post_name' ),
				'status'         => gemonio_wxr_tag_value( $item, 'wp:status' ) ?: 'publish',
				'menu_order'     => (int) gemonio_wxr_tag_value( $item, 'wp:menu_order' ),
				'content'        => gemonio_wxr_tag_value( $item, 'content:encoded' ),
				'meta'           => $meta,
				'nav_position'   => 0,
				'in_navigation'  => false,
			);
		}

		if ( 'nav_menu_item' === $post_type ) {
			$object_id = isset( $meta['_menu_item_object_id'] ) ? (int) $meta['_menu_item_object_id'] : 0;
			if ( $object_id ) {
				$menu_items[] = array(
					'page_id'  => $object_id,
					'position' => (int) gemonio_wxr_tag_value( $item, 'wp:menu_order' ),
				);
			}
		}
	}

	usort(
		$menu_items,
		static function ( array $a, array $b ): int {
			return $a['position'] <=> $b['position'];
		}
	);

	foreach ( $menu_items as $menu_item ) {
		if ( isset( $pages[ $menu_item['page_id'] ] ) ) {
			$pages[ $menu_item['page_id'] ]['nav_position']  = $menu_item['position'];
			$pages[ $menu_item['page_id'] ]['in_navigation'] = true;
		}
	}

	$ordered_pages = array_values( $pages );
	usort(
		$ordered_pages,
		static function ( array $a, array $b ): int {
			$a_pos = $a['nav_position'] ?: 999999 + $a['menu_order'];
			$b_pos = $b['nav_position'] ?: 999999 + $b['menu_order'];
			if ( $a_pos === $b_pos ) {
				return $a['id'] <=> $b['id'];
			}
			return $a_pos <=> $b_pos;
		}
	);

	return array(
		'site' => array(
			'title'       => gemonio_wxr_tag_value( $channel_head, 'title' ),
			'url'         => gemonio_wxr_tag_value( $channel_head, 'link' ),
			'description' => gemonio_wxr_tag_value( $channel_head, 'description' ),
		),
		'counts'     => $counts,
		'pages'      => $ordered_pages,
		'menu_items' => $menu_items,
	);
}

/**
 * Parse the Redux JSON export used by SCRN 2.4.
 *
 * @return array|WP_Error
 */
function gemonio_scrn_parse_redux( string $path ) {
	$raw = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $raw ) {
		return new WP_Error( 'redux_read_failed', __( 'The SCRN settings export could not be read.', 'gemonio' ) );
	}

	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'redux_invalid', __( 'The SCRN settings file is not valid JSON.', 'gemonio' ) );
	}

	return $data;
}

/**
 * Recursive conservative sanitization for settings kept only as migration reference.
 */
function gemonio_migration_sanitize_reference( $value ) {
	if ( is_array( $value ) ) {
		$clean = array();
		foreach ( $value as $key => $child ) {
			$clean[ sanitize_key( (string) $key ) ] = gemonio_migration_sanitize_reference( $child );
		}
		return $clean;
	}

	if ( is_scalar( $value ) || null === $value ) {
		return sanitize_text_field( (string) $value );
	}

	return '';
}

/**
 * Turn an SCRN page into a normalized preview row.
 */
function gemonio_scrn_preview_page( array $page ): array {
	$meta          = isset( $page['meta'] ) && is_array( $page['meta'] ) ? $page['meta'] : array();
	$source_style  = isset( $meta['_page_style'] ) ? (string) $meta['_page_style'] : '';
	$style         = '1' === $source_style ? 'Light' : ( '2' === $source_style ? 'Dark' : 'Light' );
	$fullwidth     = isset( $meta['_individual_fullwidth'] ) && '1' === (string) $meta['_individual_fullwidth'];
	$separator_url = isset( $meta['_page_sloganimg'] ) ? trim( (string) $meta['_page_sloganimg'] ) : '';

	return array(
		'id'            => (int) $page['id'],
		'title'         => (string) $page['title'],
		'slug'          => (string) $page['slug'],
		'position'      => (int) $page['nav_position'],
		'in_navigation' => ! empty( $page['in_navigation'] ),
		'style'         => $style,
		'width'         => $fullwidth ? 'Wide' : 'Standard',
		'separator_url' => $separator_url,
		'content_size'  => strlen( (string) $page['content'] ),
	);
}

/**
 * Admin handler: upload and analyze SCRN exports.
 */
function gemonio_scrn_analyze_action(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gemonio' ) );
	}
	check_admin_referer( 'gemonio_scrn_analyze' );

	$wxr_path = gemonio_migration_store_upload( 'gemonio_scrn_wxr', array( 'xml' ) );
	if ( is_wp_error( $wxr_path ) ) {
		wp_die( esc_html( $wxr_path->get_error_message() ) );
	}

	$json_path = gemonio_migration_store_upload( 'gemonio_scrn_json', array( 'json' ) );
	if ( is_wp_error( $json_path ) ) {
		@unlink( $wxr_path );
		wp_die( esc_html( $json_path->get_error_message() ) );
	}

	$wxr   = gemonio_scrn_parse_wxr( $wxr_path );
	$redux = gemonio_scrn_parse_redux( $json_path );

	if ( is_wp_error( $wxr ) || is_wp_error( $redux ) ) {
		@unlink( $wxr_path );
		@unlink( $json_path );
		$error = is_wp_error( $wxr ) ? $wxr : $redux;
		wp_die( esc_html( $error->get_error_message() ) );
	}

	$token = wp_generate_password( 20, false, false );
	set_transient(
		gemonio_migration_transient_key( $token ),
		array(
			'wxr_path'  => $wxr_path,
			'json_path' => $json_path,
			'analysis'  => array(
				'wxr'   => $wxr,
				'redux' => $redux,
			),
		),
		12 * HOUR_IN_SECONDS
	);

	wp_safe_redirect( admin_url( 'admin.php?page=gemonio-migration&token=' . rawurlencode( $token ) ) );
	exit;
}
add_action( 'admin_post_gemonio_scrn_analyze', 'gemonio_scrn_analyze_action' );

/**
 * Find or import a separator image and return an attachment id.
 *
 * @return int|WP_Error
 */
function gemonio_migration_import_remote_image( string $url ) {
	$url = trim( $url );
	if ( '' === $url ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gemonio_source_url', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $url, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$download_url = $url;
	$parsed_path  = wp_parse_url( $url, PHP_URL_PATH );
	if ( is_string( $parsed_path ) && '' !== $parsed_path ) {
		$encoded_path = str_replace( '%2F', '/', rawurlencode( rawurldecode( $parsed_path ) ) );
		$download_url = str_replace( $parsed_path, $encoded_path, $url );
	}

	$tmp = download_url( $download_url, 20 );
	if ( is_wp_error( $tmp ) ) {
		return $tmp;
	}

	$path     = wp_parse_url( $url, PHP_URL_PATH );
	$filename = $path ? basename( rawurldecode( $path ) ) : 'scrn-separator.jpg';
	$filename = sanitize_file_name( $filename ) ?: 'scrn-separator.jpg';
	$file     = array(
		'name'     => $filename,
		'tmp_name' => $tmp,
	);

	$attachment_id = media_handle_sideload( $file, 0 );
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp );
		return $attachment_id;
	}

	update_post_meta( $attachment_id, '_gemonio_source_url', $url );
	return (int) $attachment_id;
}


/**
 * Build a real WordPress one-page menu from the imported SCRN navigation
 * and assign it to Gemonio's Primary navigation location.
 *
 * The menu is deliberately owned by Gemonio. Re-running the migration
 * refreshes only this menu and never deletes unrelated user menus.
 *
 * @return array|WP_Error
 */
function gemonio_migration_create_onepage_menu( array $wxr, array $section_map ) {
	$menu_name = __( 'Gemonio One Page', 'gemonio' );
	$menu      = wp_get_nav_menu_object( $menu_name );
	$created   = false;

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $menu_id ) ) {
			return $menu_id;
		}
		$created = true;
	} else {
		$menu_id = (int) $menu->term_id;
	}

	// Rebuild only Gemonio's migration menu so repeated imports stay predictable.
	$existing_items = wp_get_nav_menu_items(
		$menu_id,
		array(
			'post_status' => 'any',
		)
	);
	if ( is_array( $existing_items ) ) {
		foreach ( $existing_items as $existing_item ) {
			wp_delete_post( (int) $existing_item->ID, true );
		}
	}

	$item_count = 0;
	foreach ( $wxr['pages'] as $page ) {
		$source_id = (int) $page['id'];
		if ( empty( $page['in_navigation'] ) || empty( $section_map[ $source_id ] ) ) {
			continue;
		}

		$section_id = (int) $section_map[ $source_id ];
		$section    = get_post( $section_id );
		if ( ! $section instanceof WP_Post ) {
			continue;
		}

		$anchor = gemonio_get_section_anchor( $section );
		$label  = trim( (string) get_post_meta( $section_id, '_gemonio_nav_label', true ) );
		$label  = '' !== $label ? $label : get_the_title( $section );

		$menu_item_id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'    => $label,
				'menu-item-url'      => home_url( '/#' . $anchor ),
				'menu-item-status'   => 'publish',
				'menu-item-type'     => 'custom',
				'menu-item-position' => ++$item_count,
			)
		);

		if ( is_wp_error( $menu_item_id ) ) {
			return $menu_item_id;
		}
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations            = is_array( $locations ) ? $locations : array();
	$locations['primary'] = (int) $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	return array(
		'id'         => (int) $menu_id,
		'name'       => $menu_name,
		'items'      => $item_count,
		'created'    => $created,
		'edit_url'   => admin_url( 'nav-menus.php?action=edit&menu=' . (int) $menu_id ),
		'assigned_to'=> 'primary',
	);
}

/**
 * Admin handler: import previously analyzed SCRN data.
 */
function gemonio_scrn_import_action(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gemonio' ) );
	}
	check_admin_referer( 'gemonio_scrn_import' );

	$token = isset( $_POST['gemonio_migration_token'] ) ? sanitize_text_field( wp_unslash( $_POST['gemonio_migration_token'] ) ) : '';
	$state = get_transient( gemonio_migration_transient_key( $token ) );
	if ( ! is_array( $state ) || empty( $state['wxr_path'] ) || empty( $state['json_path'] ) ) {
		wp_die( esc_html__( 'The migration analysis expired. Please upload the exports again.', 'gemonio' ) );
	}

	$wxr   = gemonio_scrn_parse_wxr( $state['wxr_path'] );
	$redux = gemonio_scrn_parse_redux( $state['json_path'] );
	if ( is_wp_error( $wxr ) || is_wp_error( $redux ) ) {
		$error = is_wp_error( $wxr ) ? $wxr : $redux;
		wp_die( esc_html( $error->get_error_message() ) );
	}

	if ( ! empty( $_POST['gemonio_scrn_clear_sections'] ) ) {
		$existing_sections = get_posts(
			array(
				'post_type'      => 'gemonio_section',
				'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ( $existing_sections as $section_id ) {
			wp_trash_post( (int) $section_id );
		}
	}

	$import_media = ! empty( $_POST['gemonio_scrn_import_media'] );
	$result       = array(
		'created'        => 0,
		'updated'        => 0,
		'media_imported' => 0,
		'media_failed'   => array(),
		'sections'       => array(),
		'menu'           => array(),
		'menu_error'     => '',
	);
	$section_map = array();

	foreach ( $wxr['pages'] as $index => $page ) {
		$source_id = (int) $page['id'];
		$existing  = get_posts(
			array(
				'post_type'      => 'gemonio_section',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_gemonio_source_scrn_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => (string) $source_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		$postarr = array(
			'post_type'    => 'gemonio_section',
			'post_status'  => 'publish',
			'post_title'   => wp_strip_all_tags( (string) $page['title'] ),
			'post_name'    => sanitize_title( (string) $page['slug'] ),
			'post_content' => (string) $page['content'],
			'menu_order'   => (int) $index,
		);

		if ( ! empty( $existing ) ) {
			$postarr['ID'] = (int) $existing[0];
			$section_id    = wp_update_post( wp_slash( $postarr ), true );
			$result['updated']++;
		} else {
			$section_id = wp_insert_post( wp_slash( $postarr ), true );
			$result['created']++;
		}

		if ( is_wp_error( $section_id ) ) {
			$result['sections'][] = array( 'title' => (string) $page['title'], 'status' => 'error', 'message' => $section_id->get_error_message() );
			continue;
		}

		$meta         = isset( $page['meta'] ) && is_array( $page['meta'] ) ? $page['meta'] : array();
		$source_style = isset( $meta['_page_style'] ) ? (string) $meta['_page_style'] : '1';
		$style        = '2' === $source_style ? 'dark' : 'light';
		$width        = isset( $meta['_individual_fullwidth'] ) && '1' === (string) $meta['_individual_fullwidth'] ? 'wide' : 'standard';
		$separator    = isset( $meta['_page_sloganimg'] ) ? trim( (string) $meta['_page_sloganimg'] ) : '';

		update_post_meta( $section_id, '_gemonio_source_scrn_id', (string) $source_id );
		update_post_meta( $section_id, '_gemonio_source_scrn_style', $source_style );
		update_post_meta( $section_id, '_gemonio_style', $style );
		update_post_meta( $section_id, '_gemonio_width', $width );
		update_post_meta( $section_id, '_gemonio_in_nav', ! empty( $page['in_navigation'] ) ? '1' : '0' );
		update_post_meta( $section_id, '_gemonio_nav_label', (string) $page['title'] );
		update_post_meta( $section_id, '_gemonio_anchor', sanitize_title( (string) $page['slug'] ) );
		update_post_meta( $section_id, '_gemonio_separator_source_url', $separator );
		update_post_meta( $section_id, '_gemonio_separator_height', 'normal' );
		update_post_meta( $section_id, '_gemonio_separator_text', '' );

		if ( '' !== $separator ) {
			update_post_meta( $section_id, '_gemonio_separator_type', 'parallax' );
			if ( $import_media ) {
				$image_id = gemonio_migration_import_remote_image( $separator );
				if ( is_wp_error( $image_id ) ) {
					$result['media_failed'][] = array( 'url' => $separator, 'message' => $image_id->get_error_message() );
				} elseif ( $image_id ) {
					update_post_meta( $section_id, '_gemonio_separator_image_id', (int) $image_id );
					$result['media_imported']++;
				}
			}
		} else {
			update_post_meta( $section_id, '_gemonio_separator_type', 'none' );
		}

		$section_map[ $source_id ] = (int) $section_id;

		$result['sections'][] = array(
			'title'   => (string) $page['title'],
			'status'  => 'ok',
			'edit_url'=> get_edit_post_link( $section_id, 'raw' ),
		);
	}

	$menu_result = gemonio_migration_create_onepage_menu( $wxr, $section_map );
	if ( is_wp_error( $menu_result ) ) {
		$result['menu_error'] = $menu_result->get_error_message();
	} else {
		$result['menu'] = $menu_result;
	}

	update_option( 'gemonio_scrn_reference_settings', gemonio_migration_sanitize_reference( $redux ), false );
	update_option( 'gemonio_scrn_reference_site', gemonio_migration_sanitize_reference( $wxr['site'] ), false );

	$state['result'] = $result;
	set_transient( gemonio_migration_transient_key( $token ), $state, 12 * HOUR_IN_SECONDS );

	wp_safe_redirect( admin_url( 'admin.php?page=gemonio-migration&token=' . rawurlencode( $token ) . '&imported=1' ) );
	exit;
}
add_action( 'admin_post_gemonio_scrn_import', 'gemonio_scrn_import_action' );

/**
 * Render SCRN migration admin page.
 */
function gemonio_migration_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$token = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$state = $token ? get_transient( gemonio_migration_transient_key( $token ) ) : false;
	?>
	<div class="wrap gemonio-admin-wrap gemonio-migration-wrap">
		<h1><?php esc_html_e( 'SCRN Migration', 'gemonio' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Analyze first, migrate second. Gemonio never writes anything during analysis.', 'gemonio' ); ?></p>

		<?php if ( ! is_array( $state ) || empty( $state['analysis'] ) ) : ?>
			<div class="gemonio-admin-card">
				<h2><?php esc_html_e( 'Analyze SCRN 2.x exports', 'gemonio' ); ?></h2>
				<p><?php esc_html_e( 'Upload the normal WordPress XML export and the JSON file from SCRN Options → Import / Export. Gemonio will inspect pages, navigation, SCRN metadata and global theme settings.', 'gemonio' ); ?></p>
				<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="gemonio_scrn_analyze">
					<?php wp_nonce_field( 'gemonio_scrn_analyze' ); ?>
					<p><label><strong><?php esc_html_e( 'WordPress export (.xml)', 'gemonio' ); ?></strong><br><input type="file" name="gemonio_scrn_wxr" accept=".xml,text/xml,application/xml" required></label></p>
					<p><label><strong><?php esc_html_e( 'SCRN / Redux settings (.json)', 'gemonio' ); ?></strong><br><input type="file" name="gemonio_scrn_json" accept=".json,application/json" required></label></p>
					<?php submit_button( __( 'Analyze exports', 'gemonio' ), 'primary', 'submit', false ); ?>
				</form>
			</div>
		<?php else :
			$wxr   = $state['analysis']['wxr'];
			$redux = $state['analysis']['redux'];
			$pages = isset( $wxr['pages'] ) ? $wxr['pages'] : array();
			$counts = isset( $wxr['counts'] ) ? $wxr['counts'] : array();
			$fonts = array();
			foreach ( array( 'body-white-typography', 'body-dark-typography', 'top-header-typography', 'nav-menu-typography', 'page-title-typography', 'separator-text-typography' ) as $font_key ) {
				if ( ! empty( $redux[ $font_key ]['font-family'] ) ) {
					$fonts[] = $redux[ $font_key ]['font-family'];
				}
			}
			$fonts = array_values( array_unique( $fonts ) );
			?>

			<?php if ( ! empty( $_GET['imported'] ) && ! empty( $state['result'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<?php $result = $state['result']; ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( __( 'SCRN migration completed: %1$d sections created, %2$d updated.', 'gemonio' ), (int) $result['created'], (int) $result['updated'] ) ); ?></p></div>
				<div class="gemonio-admin-card">
					<h2><?php esc_html_e( 'Import result', 'gemonio' ); ?></h2>
					<p><?php echo esc_html( sprintf( __( '%d separator media imports succeeded.', 'gemonio' ), (int) $result['media_imported'] ) ); ?></p>
					<?php if ( ! empty( $result['menu'] ) ) : ?>
						<p><strong><?php echo esc_html( sprintf( __( 'WordPress menu “%1$s” is ready with %2$d items and assigned to Primary navigation.', 'gemonio' ), (string) $result['menu']['name'], (int) $result['menu']['items'] ) ); ?></strong></p>
					<?php elseif ( ! empty( $result['menu_error'] ) ) : ?>
						<p><strong><?php esc_html_e( 'The sections were imported, but the WordPress menu could not be created:', 'gemonio' ); ?></strong> <?php echo esc_html( $result['menu_error'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $result['media_failed'] ) ) : ?>
						<p><strong><?php esc_html_e( 'Some source images could not be downloaded. Their original URLs were kept as migration references.', 'gemonio' ); ?></strong></p>
						<ul class="ul-disc">
							<?php foreach ( $result['media_failed'] as $failure ) : ?>
								<li><code><?php echo esc_html( $failure['url'] ); ?></code> — <?php echo esc_html( $failure['message'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=gemonio_section' ) ); ?>"><?php esc_html_e( 'Open imported sections', 'gemonio' ); ?></a><?php if ( ! empty( $result['menu']['edit_url'] ) ) : ?> <a class="button" href="<?php echo esc_url( $result['menu']['edit_url'] ); ?>"><?php esc_html_e( 'Open One Page menu', 'gemonio' ); ?></a><?php endif; ?> <a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View front end', 'gemonio' ); ?></a></p>
				</div>
			<?php endif; ?>

			<div class="gemonio-migration-stats">
				<div><strong><?php echo esc_html( (string) count( $pages ) ); ?></strong><span><?php esc_html_e( 'SCRN pages', 'gemonio' ); ?></span></div>
				<div><strong><?php echo esc_html( isset( $counts['nav_menu_item'] ) ? (string) $counts['nav_menu_item'] : '0' ); ?></strong><span><?php esc_html_e( 'menu items', 'gemonio' ); ?></span></div>
				<div><strong><?php echo esc_html( isset( $counts['attachment'] ) ? (string) $counts['attachment'] : '0' ); ?></strong><span><?php esc_html_e( 'attachments', 'gemonio' ); ?></span></div>
				<div><strong><?php echo esc_html( isset( $counts['post'] ) ? (string) $counts['post'] : '0' ); ?></strong><span><?php esc_html_e( 'posts', 'gemonio' ); ?></span></div>
			</div>

			<div class="gemonio-admin-card gemonio-admin-card--wide">
				<h2><?php esc_html_e( 'Recognized one-page structure', 'gemonio' ); ?></h2>
				<p><?php esc_html_e( 'The navigation order from the WordPress menu is used as the Gemonio section order. SCRN page metadata is translated into Gemonio section settings.', 'gemonio' ); ?></p>
				<div class="gemonio-table-scroll">
					<table class="widefat striped gemonio-migration-table">
						<thead><tr><th>#</th><th><?php esc_html_e( 'SCRN page', 'gemonio' ); ?></th><th><?php esc_html_e( 'Anchor', 'gemonio' ); ?></th><th><?php esc_html_e( 'Style', 'gemonio' ); ?></th><th><?php esc_html_e( 'Width', 'gemonio' ); ?></th><th><?php esc_html_e( 'After page', 'gemonio' ); ?></th></tr></thead>
						<tbody>
						<?php foreach ( $pages as $page ) : $preview = gemonio_scrn_preview_page( $page ); ?>
							<tr>
								<td><?php echo esc_html( $preview['position'] ? (string) $preview['position'] : '–' ); ?></td>
								<td><strong><?php echo esc_html( $preview['title'] ); ?></strong><br><small>SCRN ID <?php echo esc_html( (string) $preview['id'] ); ?> · <?php echo $preview['in_navigation'] ? esc_html__( 'in menu', 'gemonio' ) : esc_html__( 'not in menu', 'gemonio' ); ?></small></td>
								<td><code>#<?php echo esc_html( $preview['slug'] ); ?></code></td>
								<td><?php echo esc_html( $preview['style'] ); ?></td>
								<td><?php echo esc_html( $preview['width'] ); ?></td>
								<td><?php echo $preview['separator_url'] ? '<span class="gemonio-badge">' . esc_html__( 'Parallax image', 'gemonio' ) . '</span>' : '–'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="gemonio-admin-card gemonio-admin-card--wide">
				<h2><?php esc_html_e( 'Global SCRN reference', 'gemonio' ); ?></h2>
				<dl class="gemonio-reference-list">
					<dt><?php esc_html_e( 'Site', 'gemonio' ); ?></dt><dd><?php echo esc_html( isset( $wxr['site']['title'] ) ? $wxr['site']['title'] : '' ); ?></dd>
					<dt><?php esc_html_e( 'Hero title', 'gemonio' ); ?></dt><dd><?php echo esc_html( isset( $redux['topheader_text'] ) ? $redux['topheader_text'] : '' ); ?></dd>
					<dt><?php esc_html_e( 'Hero image', 'gemonio' ); ?></dt><dd><code><?php echo esc_html( isset( $redux['bg_image1']['url'] ) ? $redux['bg_image1']['url'] : '' ); ?></code></dd>
					<dt><?php esc_html_e( 'Fonts found', 'gemonio' ); ?></dt><dd><?php echo esc_html( implode( ', ', $fonts ) ); ?></dd>
					<dt><?php esc_html_e( 'Home menu link', 'gemonio' ); ?></dt><dd><?php echo ! empty( $redux['menu_homelink'] ) ? esc_html__( 'Yes', 'gemonio' ) : esc_html__( 'No', 'gemonio' ); ?></dd>
				</dl>
				<p class="description"><?php esc_html_e( 'Global SCRN styling is stored as a reference, not blindly applied. Gemonio keeps its own design language while preserving the migration evidence.', 'gemonio' ); ?></p>
			</div>

			<div class="gemonio-admin-card">
				<h2><?php esc_html_e( 'Run migration', 'gemonio' ); ?></h2>
				<p><?php esc_html_e( 'The old page HTML is preserved as-is inside each Gemonio Section. That gives us the best possible reference before we modernize individual content later.', 'gemonio' ); ?></p>
				<p><?php esc_html_e( 'Gemonio also creates or refreshes a real WordPress menu named “Gemonio One Page” from the SCRN menu order and assigns it to Primary navigation.', 'gemonio' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="gemonio_scrn_import">
					<input type="hidden" name="gemonio_migration_token" value="<?php echo esc_attr( $token ); ?>">
					<?php wp_nonce_field( 'gemonio_scrn_import' ); ?>
					<p><label><input type="checkbox" name="gemonio_scrn_import_media" value="1" checked> <?php esc_html_e( 'Try to import SCRN separator images into the Media Library', 'gemonio' ); ?></label></p>
					<p><label><input type="checkbox" name="gemonio_scrn_clear_sections" value="1"> <?php esc_html_e( 'Move existing Gemonio Sections to Trash before import', 'gemonio' ); ?></label><br><small><?php esc_html_e( 'Useful for a clean SCRN reconstruction. This is optional and deliberately off by default.', 'gemonio' ); ?></small></p>
					<?php submit_button( __( 'Migrate SCRN to Gemonio', 'gemonio' ), 'primary', 'submit', false ); ?>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=gemonio-migration' ) ); ?>"><?php esc_html_e( 'Start over', 'gemonio' ); ?></a>
				</form>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
