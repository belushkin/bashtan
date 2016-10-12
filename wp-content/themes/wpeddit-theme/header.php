<?php

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<?php 
if(wp_is_mobile()){
	$html_id = "mobile";
}else{
	$html_id = "desktop";
}
?>
<html <?php language_attributes(); ?> xmlns:fb="http://ogp.me/ns/fb#" id="<?php echo $html_id; ?>">
  <head>
    <?php  $desc = esc_html(wp_trim_words( $post->post_content , 40, '...' )); ?>
    <meta charset="utf-8">
	<title><?php wp_title( '', true, 'right' );?></title>
    <meta name="description" content="<?php echo $desc; ?>">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">

    <meta name="twitter:widgets:csp" content="on">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    
<script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
 
  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };
 
  return t;
}(document, "script", "twitter-wjs"));</script>

<?php  		
global $post;
#}code used for throttling of submitted content...
$current_user = wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {
    $ehacklast = get_user_meta($current_user->ID, 'ehacklast', true);
    $ehacksince = time() - $ehacklast;
    echo '<script>var ehacklast = ' . $ehacksince . '</script>';
} ?>
	

    <meta property="og:title" content="<?php wp_title(); ?>"/>
    <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
    <meta property="og:description" content="<?php echo $desc; ?>"/> 

<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' ); ?>
 <meta property="og:image" content="<?php echo $image[0]; ?>"/> 

   
<?php
if ( ! isset( $content_width ) ) $content_width = 900;

wp_head();
	$logged_in = is_user_logged_in();
	if($logged_in){
		$in = 'yes';
	}else{
		$in = 'no';
	}
	echo "<script>var wpeddit_loggedin ='" . $in ."';</script>";
	?>
</head>
	<?php
	$defaults = array(
		'theme_location'  => 'header-menu',
		'container'       => 'div',
		'container_class' => 'container',
		'menu_class'      => 'menu',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'items_wrap'      => '<ul id="%1$s" class="nav">%3$s</ul>',
		'depth'           => 0
	);
	?>
	<?php $args = array(
		'show_option_all'    => '',
		'orderby'            => 'name',
		'parent' 			 => 0,
		'order'              => 'ASC',
		'style'              => 'list',
		'show_count'         => 0,
		'hide_empty'         => 0,
		'use_desc_for_title' => 1,
		'child_of'           => 0,
		'feed'               => '',
		'feed_type'          => '',
		'feed_image'         => '',
		'exclude'            => '',
		'exclude_tree'       => '',
		'include'            => '',
		'hierarchical'       => 1,
		'title_li'           => '',
		'show_option_none'   => '',
		'number'             => '',
		'echo'               => 1,
		'depth'              => 0,
		'current_category'   => 0,
		'pad_counts'         => 0,
		'taxonomy'           => 'category',
		'walker'             => null
	); ?>
	
<?php global $current_user;
      wp_get_current_user();

$taxonomy = 'category';
$terms = get_terms($taxonomy);

if ($terms) {
 $count = 0;
  $random = rand(0,count($terms)-1);  //get a random number
  foreach( $terms as $term ) {
    $count++;
    if ($count == $random ) {  // only if count is equal to random number display get posts for that category
     $random =  $term->term_id;

    }
  }
  
}
?>
<body <?php body_class(); ?>>
<div id="page" class="site">
	<div id = "sr-header-area">	
		<div class = "width-clip">
				<ul class="inline">
					<span class="wpeddit-dd">
						<li class="dropdown cat-item">
	                      <span id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php _e('my','wpeddit');?> <?php echo of_get_option('wpeddit_name','wpeddits');?><b class="caret"></b></span>
	                      <ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
	                      	<?php 
							if(is_user_logged_in()){
								$subs = get_user_meta($current_user->ID,'wpeddit_subs',true);
								$subs_arr = explode(',',$subs);
								foreach($subs_arr as $sub){
									if($sub == ''){
									  continue;
									}
									$term = get_term($sub); 
									?>
									      <li role="presentation"><a href = "<?php echo get_category_link( $term->term_id ); ?>"><?php echo $term->name;?></a></li>
									<?php
								}
							} ?>
	                        <li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo of_get_option('wpeddit_more_cat','#');?>#wpeddit-my-sub" class = 'reddit-dotty'>manage subscriptions</a></li>
	                      </ul>
	                    </li>
                	</span>
					<span class = 'reddit-left-menu'>
						<li><a href="<?php echo home_url();?>" title="" style = "color:red;font-weight:bold"><?php _e('front','wpeddit');?></a></li>
						<li><a href = "<?php echo get_category_link( $random ); ?>"><?php _e('Random','wpeddit');?></a></li>
						<li>|</li>
		                    <?php wp_list_categories($args); ?> 
		            </span>
		            <span class="reddit-more">
		                <li class = 'pull-right'><a href='<?php echo of_get_option('wpeddit_more_cat','#');?>'><b><?php _e('More','wpeddit');?> >></b></a></li>
		            </span>
		        </ul>
		</div>	
	</div>
	<div id="header-bottom-left">
		<a href="<?php echo home_url();?>"><img class='logo' src='<?php echo of_get_option('main_logo'); ?>'/></a>&nbsp;
		<?php  if(!is_user_logged_in()) { ?>
		<div id="header-bottom-right" class = 'pull-right screen-only'><span class="user">want to join? <a href="#myModal" data-toggle="modal" class="login-required">login or register</a> in seconds</span>
		<?php }else{  ?>
		<div id="header-bottom-right" class = 'pull-right screen-only'><span class="user">Welcome <a href="<?php echo get_author_posts_url($current_user->ID);?>"><?php echo $current_user->display_name;?></a></span>
			<span class="separator">|</span><a href="<?php echo wp_logout_url(); ?>" title="Logout">logout</a>	
		<?php } ?>
	</div>

</div>