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
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">


		<div class='navigation-banner'>
	
				<ul class='post-order'>
					<li><a href='#' class='active' id="hot">гаряче</a></li>
					<li><a href='#' id="new">нове</a></li>
					<li><a href='#' id="rising">піднімається</a></li>
					<li><a href='#' id="contro">суперечливе</a></li>
					<li><a href='#' id="top">топ</a></li>					
				</ul>
		
		</div>


		<div id="wpeddit_post_url" data-url="<?php echo get_home_url(); ?>" class="hide"></div>
		<div id="wpeddit_cat" data-cat="<?php echo $cat; ?>" class="hide"></div>
		<?php 

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		echo '<div id="wpedditpaged" data-page="' . $paged .'" class="hide"></div>';
		?>
		<div id="wrapper" class="reddit-left">
		<main id="main" class="site-main" role="main">

		<?php

		$wpeddittab = 'hot';
		if(isset($_GET['tab'])){
			$wpeddittab = $_GET['tab'];
		}

		if(isset($_GET['cat'])){
			$cat = $_GET['cat'];
		}


		if(isset($_GET['wpedditpaged'])){
			$paged = $_GET['wpedditpaged'];
		}

		//get the users subscriptions?
		global $current_user;
		$mysubs = get_user_meta($current_user->ID,'wpeddit_subs',true);
		$mysubs_array = explode(',',$mysubs);
		if($mysubs_array == ''){
			$mysubs_array = 1;  //gives the 'Uncategorized' category by default (for those logged in)...
		}

		array_push($mysubs_array, 1);  //put this in the settings. Will make sure anything 'uncategorized' shows on home regardless

		if($wpeddittab == 'top'){
			if(is_user_logged_in()){
				$args = array(
				    'meta_key'      => 'epicredvote',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat,
				    'category__in' => $mysubs_array
				);
				query_posts( $args );
			}else{
				$args = array(
				    'meta_key'      => 'epicredvote',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat
				);
				query_posts( $args );				
			}
		}elseif($wpeddittab == 'hot'){
			if(is_user_logged_in()){
				$args = array(
				    'meta_key'      => 'epicredrank',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat,
				   	'category__in' => $mysubs_array
				);
				query_posts( $args );
			}else{
				$args = array(
				    'meta_key'      => 'epicredrank',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat
				);
				query_posts( $args );
			}
		}elseif($wpeddittab == 'new'){
			if(is_user_logged_in()){
				$args = array(
				    'orderby'     => 'post_date',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat,
				    'category__in' => $mysubs_array
				);
				query_posts( $args );		
			}else{
				$args = array(
				    'orderby'     => 'post_date',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat
				);
				query_posts( $args );	
			}
		}elseif($wpeddittab == 'contro'){
			if(is_user_logged_in()){
				$args = array(
				    'meta_key'      => 'wpeddit_contro',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat,
				    'category__in' => $mysubs_array
				);
				query_posts( $args );
			}else{
				$args = array(
				    'meta_key'      => 'wpeddit_contro',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat
				);
				query_posts( $args );				
			}
		}elseif($wpeddittab == 'rising'){
			if(is_user_logged_in()){
				$args = array(
				    'meta_key'      => 'wpeddit_rising',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat,
				    'category__in' => $mysubs_array
				);
				query_posts( $args );
			}else{
				$args = array(
				    'meta_key'      => 'wpeddit_rising',
				    'orderby'     => 'meta_value_num',
				    'order'    => 'DESC',
				    'paged'		=> $paged,
				    'cat'		=> $cat
				);
				query_posts( $args );				
			}
		}

		?>

		<?php if ( have_posts() ) : ?>
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/content' );
			endwhile;

			?>
		</main><!-- .site-main -->
		<div class='loading'>
			<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>
		</div>
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
		<?php
		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>


		<?php get_sidebar(); ?>
	</div><!-- .content-area -->
<?php get_footer(); ?>
