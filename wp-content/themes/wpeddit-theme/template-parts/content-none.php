<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage WPeddit Theme
 * @since WPeddit Theme 2.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="nothing-here">
		<?php _e('Sorry, nothing has been posted here yet','wpeddit'); ?>
	</div>
</article><!-- #post-## -->

