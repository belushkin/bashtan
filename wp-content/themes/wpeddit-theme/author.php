<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage WPeddit
 * @since WPeddit Theme 2.0
 */

get_header(); 

$current_user = wp_get_current_user();
global $author;
if($current_user->ID == $author){
	$myprofile = true;
}

$user_info = get_userdata($author);
global $username;
$username = $user_info->display_name;

  global $wpdb;
  
  $wpdb->myo_ip   = $wpdb->prefix . 'epicred';

	$q1 = "SELECT epicred_id FROM $wpdb->myo_ip JOIN $wpdb->posts ON $wpdb->myo_ip.epicred_id = $wpdb->posts.ID WHERE epicred_ip = $author AND epicred_option > '0' AND $wpdb->posts.post_type = 'post' AND $wpdb->posts.post_status = 'publish'";
	$upvoted = $wpdb->get_results($q1, OBJECT);

	$q2 = "SELECT epicred_id FROM $wpdb->myo_ip JOIN $wpdb->posts ON $wpdb->myo_ip.epicred_id = $wpdb->posts.ID WHERE epicred_ip = $author AND epicred_option = '-1' AND $wpdb->posts.post_type = 'post' AND $wpdb->posts.post_status = 'publish'";
	$downvoted = $wpdb->get_results($q2, OBJECT);


?>

	<div id="primary" class="content-area">
		<div class='navigation-banner'>
				<ul class='post-order'>
					<li><a href='#' id="submitted" class='active'>submitted</a></li>
					<li><a href='#' id="wpedditcomment">comments</a></li>
					<?php if($myprofile){ ?>
					<li><a href='#' id="upvotes">upvoted</a></li>
					<li><a href='#' id="downvotes">downvoted</a></li>
					<?php } ?>					
				</ul>
		</div>

			<header class="entry-header-author">
			<h2 class="entry-title"><?php echo $username; ?></h2>
			</header><!-- .entry-header -->

		<div id="wrapper">
		<main id="main" class="site-main reddit-left" role="main">
		<div id="wpeddit-author-submitted" class="wpeddit-author-tab">
		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/content', get_post_format() );
			endwhile;

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
		</div>
		<div id="wpeddit-author-upvotes" class="hide wpeddit-author-tab">
			<?php foreach ($upvoted as $ID):
		      $post = get_post($ID->epicred_id); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php epic_reddit_voting($post->ID); ?>
					<?php wpeddit_thumbnail(); ?>
					<?php wpeddit_outputlink($post->ID); ?>
						<footer class="entry-footer">
							<div class='posted-on'><?php _e('submitted','wpeddit'); ?> <?php printf( _x( '%s ago', '%s = human-readable time difference', 'wpeddit' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> <?php _e('by','wpeddit'); ?> <?php the_author_posts_link(); ?>   <?php _e('to','wpeddit');?> <?php echo get_the_category_list( ',', '', $post->ID ); ?></div>
							<div class='meta'>
								<a href="<?php comments_link(); ?>"><?php comments_number( 'comment', 'one comment', '% comments' ); ?></a>
								<a href="#" class='show-share' data-wpid="<?php echo $post->ID; ?>"><?php _e('share','wpeddit'); ?></a>
							</div>

							<div class='post-share hide wpshareblock-<?php echo $post->ID; ?>'>
								<div class='close-share'>X</div>
								<div class='swrapp'>
									<span class='lb'><?php _e("Share with: ", "wpeddit"); ?></span>
									<a class="share" href="<?php echo get_permalink($post->ID);?>" title="<?php the_title(); ?>" data-action="facebook"><li class='fb ph-s'><i class="fa fa-facebook"></i></li></a>
									<a class="share" href="<?php echo get_permalink($post->ID);?>" title="<?php the_title(); ?>" data-action="twitter"><li class='tw ph-s'><i class="fa fa-twitter"></i></li></a>						
									<br/>
								</div>
								<span class='lb'><?php _e("Link: ", "wpeddit"); ?></span> <span class='wp-link'><?php echo get_permalink($post->ID); ?></span>
							</div>
							
							<div class='clear'></div>
						</footer>
				</article><!-- #post-## -->
				<div class="clear"></div>
 			<?php endforeach; ?>
		</div>

		<div id="wpeddit-author-downvotes" class="hide wpeddit-author-tab">
			<?php foreach ($downvoted as $ID):
		      $post = get_post($ID->epicred_id); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php epic_reddit_voting($post->ID); ?>
					<?php wpeddit_thumbnail(); ?>
					<?php wpeddit_outputlink($post->ID); ?>
						<footer class="entry-footer">
							<div class='posted-on'><?php _e('submitted','wpeddit'); ?> <?php printf( _x( '%s ago', '%s = human-readable time difference', 'wpeddit' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> <?php _e('by','wpeddit'); ?> <?php the_author_posts_link(); ?>   <?php _e('to','wpeddit');?> <?php echo get_the_category_list( ',', '', $post->ID ); ?></div>
							<div class='meta'>
								<a href="<?php comments_link(); ?>"><?php comments_number( 'comment', 'one comment', '% comments' ); ?></a>
								<a href="#" class='show-share' data-wpid="<?php echo $post->ID; ?>"><?php _e('share','wpeddit'); ?></a>
							</div>

							<div class='post-share hide wpshareblock-<?php echo $post->ID; ?>'>
								<div class='close-share'>X</div>
								<div class='swrapp'>
									<span class='lb'><?php _e("Share with: ", "wpeddit"); ?></span>
									<a class="share" href="<?php echo get_permalink($post->ID);?>" title="<?php the_title(); ?>" data-action="facebook"><li class='fb ph-s'><i class="fa fa-facebook"></i></li></a>
									<a class="share" href="<?php echo get_permalink($post->ID);?>" title="<?php the_title(); ?>" data-action="twitter"><li class='tw ph-s'><i class="fa fa-twitter"></i></li></a>						
									<br/>
								</div>
								<span class='lb'><?php _e("Link: ", "wpeddit"); ?></span> <span class='wp-link'><?php echo get_permalink($post->ID); ?></span>
							</div>
							
							<div class='clear'></div>
						</footer>
				</article><!-- #post-## -->
				<div class="clear"></div>
 			<?php endforeach; ?>
		</div>

		<div id="wpeddit-author-wpedditcomment" class="hide wpeddit-author-tab">
		<?php
	     $args = array(
	            'user_id' => $author,
	            'status' => 'approve'
	            );

	        $comments = get_comments( $args );

	        if ( $comments )
	        {

	            foreach ( $comments as $c )
	            { ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php  epic_reddit_voting_comment($c->comment_ID); ?>
					<header class="entry-header">
						<h2 class="entry-title"><a href="<?php echo get_permalink($c->comment_post_ID); ?>"><?php echo get_the_title($c->comment_post_ID); ?></a></h2>
					</header><!-- .entry-header -->
						<footer class="entry-footer">
							<div class="comment_content">
								<?php echo $c->comment_content; ?>
							</div>
							
							<div class='clear'></div>
						</footer>
				</article><!-- #post-## -->

	        	<?php
	            $output.= '<div class="wpeddit-comm">
							<p class="title"><a href="'.get_comment_link( $c->comment_ID ).'">';
	            $output.= get_the_title($c->comment_post_ID);
	            $output.= '</a></p><div class="tagline">';
	            $output.= $c->comment_content;
	            $output.= '</div></div>';
	            }

	           // echo $output;
	        } else { 
	            echo "This user has not made any comments";
	        }
		?>
		</div>


		</main><!-- .site-main -->
		<div class='loading'>
			<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>
		</div>
		<div class='clear'></div>
		<div class='navigation'>
			<?php
			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( '< previous', 'wpeddit' ),
				'next_text'          => __( 'next >', 'wpeddit' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'wpeddit' ) . ' </span>',
			) );
			?>
		</div>
	</div>  <!-- end #wrapper -->


		<?php get_sidebar('author'); ?>
	</div><!-- .content-area -->
<?php get_footer(); ?>
