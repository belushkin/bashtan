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

?>

	<div id="primary" class="content-area">


		<div class='navigation-banner'>
	
				<ul class='post-order'>
					<li><a href='#' class='active'>comments</a></li>				
				</ul>
		
		</div>

			<header class="entry-header-author">
			<h2 class="entry-title"><?php echo $username; ?></h2>
			</header><!-- .entry-header -->

		<main id="main" class="site-main reddit-left" role="main">
		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/single' );
			endwhile;

			// Previous/next page navigation.


		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- .site-main -->
		<?php get_sidebar(); ?>
	</div><!-- .content-area -->
<?php get_footer(); ?>
