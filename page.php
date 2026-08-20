<?php
/**
 * Page template.
 *
 * @package Gemonio
 */
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'site-shell content-shell single-entry' ); ?>>
		<header class="entry-header"><h1><?php the_title(); ?></h1></header>
		<div class="entry-content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;
get_footer();
