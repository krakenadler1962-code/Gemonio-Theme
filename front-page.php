<?php
/**
 * Front page template.
 *
 * Published Gemonio Sections form the one-page site. Existing front-page
 * content remains visible as a graceful fallback until sections are created.
 *
 * @package Gemonio
 */
get_header();

$gemonio_sections = gemonio_get_sections();

if ( ! empty( $gemonio_sections ) ) {
	foreach ( $gemonio_sections as $gemonio_section ) {
		gemonio_render_section( $gemonio_section );
	}
} else {
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'gemonio-front-page' ); ?>>
			<?php the_content(); ?>
		</article>
		<?php
	endwhile;
}

get_footer();
