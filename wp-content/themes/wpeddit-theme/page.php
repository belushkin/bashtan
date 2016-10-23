<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */
get_header(); 
global $post;
?>
<div id="primary" class="content-area">
    <table>
        <tr>
            <td>
                <main id="main" class="site-main reddit-left" role="main">
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                        <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                        </header><!-- .entry-header -->
                        <?php echo apply_filters( 'the_content',$post->post_content); ?>
                    </article><!-- #post-## -->
                </main><!-- .site-main -->
            </td>
    <?php get_sidebar(); ?>
</div><!-- .content-area -->
<?php get_footer(); ?>