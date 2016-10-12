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
	<?php epic_reddit_voting($post->ID); ?>
	<?php wpeddit_thumbnail(); ?>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->

	<?php // wpeddit_excerpt(); ?>


		<footer class="entry-footer">
			<div class='main-info-wrap'>
				<div class='posted-on'><?php _e('submitted','wpeddit'); ?> <?php printf( _x( '%s ago', '%s = human-readable time difference', 'wpeddit' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> <?php _e('by','wpeddit'); ?> <?php the_author_posts_link(); ?>   <?php _e('to','wpeddit');?> <?php echo get_the_category_list( ',', '', $post->ID ); ?></div>
				<div class='meta'>
					<a href="<?php comments_link(); ?>"><?php comments_number( 'comment', 'one comment', '% comments' ); ?></a>
					<a href="#" class='show-share-sin' data-wpid="<?php echo $post->ID; ?>"><?php _e('share','wpeddit'); ?></a>
				</div>
				<div class='post-share-sin hide' id="wpshareblock-<?php echo $post->ID; ?>">
					<div class='close-share-sin'>X</div>
					<div class='swrapp'>
						<span class='lb'><?php _e("Share with: ", "wpeddit"); ?></span>
						<a class="share" href="<?php echo get_permalink($post->ID);?>" title="<?php the_title(); ?>" data-action="facebook"><li class='fb ph-s'><i class="fa fa-facebook"></i></li></a>
						<a class="share" href="<?php echo get_permalink($post->ID);?>" title="<?php the_title(); ?>" data-action="twitter"><li class='tw ph-s'><i class="fa fa-twitter"></i></li></a>						
						<br/>
					</div>
					<span class='lb'><?php _e("Link: ", "wpeddit"); ?></span> <span class='wp-link'><?php echo get_permalink($post->ID); ?></span>
				</div>
			</div>

			<div class="wpeddit-content">
				<?php the_content(); ?>
			</div>


			
			<div class='clear'></div>
		</footer>
</article><!-- #post-## -->

<?php comments_template(); ?>

