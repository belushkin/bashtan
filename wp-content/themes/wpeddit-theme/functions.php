<?php

 global $wpeddit_theme_version;
 $wpeddit_theme_version = '2.0';






/* Notice for the MySQL table generation */
function wpeddit_tables_not_created() {
  global $wpdb;
  $table_name = $wpdb->prefix . "epicred";
  $q = "SHOW TABLES LIKE '$table_name'";
  $result = $wpdb->query($q);
  if($result == 0){
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    epicred_id mediumint(9) NOT NULL,
    epicred_option mediumint(9) NOT NULL,
    epicred_ip text NOT NULL,
    UNIQUE KEY id (id)
      );";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
   
  $table_name = $wpdb->prefix . "epicred_comment";
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    epicred_id mediumint(9) NOT NULL,
    epicred_option mediumint(9) NOT NULL,
    epicred_ip text NOT NULL,
    UNIQUE KEY id (id)
      );";

     dbDelta($sql);
   }
}
add_action( 'admin_notices', 'wpeddit_tables_not_created' );


function wpeddit_post_ranking($post_id){
  
  $x = get_post_meta($post_id, 'epicredvote', true );
  if($x == ""){
    $x = 0;
  }
  
  $ts = get_the_time("U",$post_id);
  
  if($x > 0){
    $y = 1;
  }elseif($x<0){
    $y = -1;
  }else{
    $y = 0;
  }
  
  $absx = abs($x);
  if($absx >= 1){
    $z = $absx;
  }else{
    $z = 1;
  }
  $rating = log10($z) + (($y * $ts)/45000);
  update_post_meta($post_id,'epicredrank',$rating);
  return $rating;
}



function wpeddit_plugin_active(){
  if (function_exists('wpeddit_retrieveNews')) {
  //plugin is activated
  ?>
  <div class="notice notice-warning">
        <p><?php _e( 'You have the WPeddit plugin active. This is no longer needed. Please deactivate!', 'wpeddit' ); ?></p>
    </div>
  <?php
  }  
}
add_action( 'admin_notices', 'wpeddit_plugin_active' );




/* Theme Options */
require_once dirname( __FILE__ ) . '/inc/options-framework.php';

define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
define( 'PLUGINHUNT_IMAGES', get_template_directory_uri() . '/images/' );


add_action( 'optionsframework_custom_scripts', 'optionsframework_custom_scripts' );

$optionsfile = locate_template( 'options.php' );
load_template( $optionsfile );

function optionsframework_custom_scripts() { ?>
  <script type="text/javascript">
  jQuery(document).ready(function() {
    
    jQuery('#mailchimp_showhidden').click(function() {
    });                  

    jQuery("#epic-form-submit-button" ).click(function() {
        console.log('button clicked to submit');
        jQuery( "#epic_options_form" ).submit();
    });

    jQuery('.nav-tab-epic').click(function(e){

      e.preventDefault();
      var ph_settings_tab = jQuery(this).data('tab');

      jQuery('.group').removeClass('active').addClass('hide');
      jQuery('#'+ ph_settings_tab).removeClass('hide').addClass('active');

      if(jQuery(this).hasClass('active')){
        return false;
      }else{
        jQuery('.nav-tab-epic').removeClass('active');
        jQuery(this).addClass('active');
      }
    });

    if (jQuery('#example_showhidden:checked').val() !== undefined) {
      jQuery('#section-example_text_hidden').show();
    }

  });
  </script>
<?php }


add_theme_support( 'automatic-feed-links' );
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
add_theme_support( 'post-formats', array(
		'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
) );
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 604, 270, true );

//initial theme options
  add_option('wpedditstyle','default','','yes');
	add_option('wpmenu','fixed','','yes');
	add_option('wplogo','','','yes');


//enqueue the styles. This will be where the theme style chooser comes in for the admin side of the site.
/** media uploader  **/
function wpeddit_add_media_upload_scripts() {
    if ( is_admin() ) {
         return;
       }
    wp_enqueue_media();
}
add_action('wp_enqueue_scripts', 'wpeddit_add_media_upload_scripts');

#} filter the code for the media uploader
add_filter('media_upload_default_tab', 'wpeddit_switch_tab');
function wpeddit_switch_tab($tab){
  if(!is_admin()){
    return 'type';
  }
}

function wpeddit_subwpeddit_create(){

global $post, $wpdb,$current_user,$wp;
    wp_get_current_user();  


if(isset($_POST['save'])){
  // we are saving the post
  $title = $_POST['title'];
  $content = $_POST['content'];

  
  if(get_option('wpedditnewpost') == 'publish'){
  $status = 'publish';
  }else{
  $status = 'pending';
  }
  
  $posttype = 'post';
  $author = $current_user->ID;
  $taxonomy = 'category';

  $arg = array('description' => $content, 'parent' => 0);
  $new_cat_id = wp_insert_term($title, "category", $arg);
  
  ?>
    
  <div class="wpeddit-alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Success!</strong> Your category has been created.
  </div>
  
  
  <style>
    #thumbnail_upload,.cat-submitted{
      display:none;
    }
  </style>

<?php



  
}


if(!is_user_logged_in()) {
  
  echo "Sorry you must be logged in to do this";
  
}elseif(!current_user_can( 'edit_posts' )){
   _e("You need to have permission to create sub-reddits. Please contact the site admin","wpeddit"); 
}else{ 

?>
<div id="wpeddit_submit" class="cat-submitted">
  <div class='tab-content'>
    <form id="thumbnail_upload" method="post" action="">
      <div class='form-fields url'>
        <label>title</label>
        <input type="text" name="title" id = "title"></br><br/>
      </div>
      <div style = "clear:both"></div>
      <div class='form-fields url'>
        <label>description</label>
        <textarea name="content" id="content"  rows="10" tabindex="4"></textarea>
        <input type = "hidden" name = "save" value = "1">
      </div>
      <div style = "clear:both"></div>
          <input id="wpeddit-subcreate" class = "btn btn-primary"  name="wpeddit-subcreate" type="submit" value="Submit">
      </form>
    </div>
</div>

<?php

}

}

add_shortcode("wpeddit_subwpeddit_create", "wpeddit_subwpeddit_create");



//default
function wpeddit_create(){

global $post, $wpdb,$current_user,$wp;
    wp_get_current_user();	
if(!is_user_logged_in()) {
	
	_e("Sorry you must be logged in to do this","wpeddit");
	
}elseif(!current_user_can( 'edit_posts' )){
   _e("You need to have permission to post. Please contact the site admin","wpeddit"); 
}else{ 

?>
<div id="wpeddit_submit">
    <div class='tabs'>
      <ul class='tab-menu'>
        <li id='link' class='active wpeddit-tab'><?php _e('link','wpeddit'); ?></li>
        <li id='text' class='wpeddit-tab'><?php _e('text','wpeddit'); ?></li>
      </ul>
    </div> 
    <div class='tab-content'>
      <div class='link-tc tab'>
        <div class="wpeddit-alert">
        You are submitting a link. The key to a successful submission is interesting content and a descriptive title.
        </div>
        <form id="link-submit" method="post" action="">
            <div class='form-fields url'>
              <label><?php _e('url','wpeddit'); ?></label>
              <input type="text" id="wpeddit-url" name="wpeddit-url"/>
            </div>
            <div class='form-fields image-url'>
              <label><?php _e('image','wpeddit'); ?></label>
              <input type="hidden" id="wpeddit-img-url" name="wpeddit-img-url"/>
              <div class="trigger-wrap">
                <i class="fa fa-photo"></i><a class="trigger-upload" href="#" id="_unique_name_button" data-pid='1'><?php _e('Upload an image','pluginhunt');?></a><input accept="image/gif, image/jpeg, image/png" class="uploader" type="file">
              </div>
              <div class="wpeddit-image-wrap">
                  <img src="" id="wpeddit-image-here" style="max-height:500px;"/>
              </div>
            </div>
            <div class='form-fields link-title'>
              <label><?php _e('title','wpeddit'); ?></label>
              <input type="text" id="wpeddit-link-title" name="wpeddit-link-title"/>
              <input type = "hidden" name = "save" value = 1>
            </div>
            <div class="form-fields wpeddit-select">
              <label><?php _e('choose a sub','wpeddit'); ?><?php echo of_get_option('wpeddit_name','wpeddit'); ?></label>
              <?php wp_dropdown_categories( 'hide_empty=0' ); ?>
            </div>
        </form>
      </div>
      <div class='text-tc hide tab'>
        <div class="wpeddit-alert">
You are submitting a text-based post. Speak your mind. A title is required, but expanding further in the text field is not. Beginning your title with "vote up if" is violation of intergalactic law.
        </div>       
        <form id="thumbnail_upload" method="post" action="">
            <div class='form-fields url'>
              <label><?php _e('title','wpeddit'); ?></label>
              <input type="text" id="wpeddit-text-title" name="wpeddit-text-title"></br><br/>
            </div>
          <div style = "clear:both"></div>

            <div class='form-fields description'>
              <label><?php _e('description','wpeddit'); ?></label>
          <textarea id="wpeddit-text-content" name="wpeddit-text-content" rows="10" tabindex="4"></textarea>
          <input type = "hidden" name = "save" value = 1>
              </div>
          <div style = "clear:both"></div>
            <div class="form-fields wpeddit-select">
              <label><?php _e('choose a sub','wpeddit'); ?><?php echo of_get_option('wpeddit_name','wpeddit'); ?></label>
              <?php wp_dropdown_categories( 'hide_empty=0&id=textcat' ); ?>
            </div>

        </form>
      </div>
    </div>
    <?php
    echo '<input type="hidden" name="wpedditsub-ajax-nonce" id="wpedditsub-ajax-nonce" value="' . wp_create_nonce( 'wpedditsub-ajax-nonce' ) . '" />';
    ?>
</div>
	<input id="wpedditfront" class = "btn btn-primary"  name="wpedditfront" type="submit" value="Submit">

<?php

}

}
add_shortcode("wpedditcreate", "wpeddit_create");

function wpeddit_outputlink($post_id){
            $wpeddit_out = get_post_meta($post_id,'wpeddit_out',true);
            if($wpeddit_out == ''){
              $post_link = get_permalink();
            }else{
              $post_link = $wpeddit_out;
              $target = '_blank';
            }
          ?>
          <header class="entry-header">
            <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" target="'.$target.'" rel="bookmark">', esc_url( $post_link ) ), '</a></h2>' ); ?>
          </header><!-- .entry-header -->
<?php }



add_action( 'wp_ajax_nopriv_wpeddit_submitnew', 'wpeddit_submitnew' );
add_action( 'wp_ajax_wpeddit_submitnew', 'wpeddit_submitnew' );
function wpeddit_submitnew(){
  check_ajax_referer( 'wpedditsub-ajax-nonce', 'security' );
  global $current_user;
  //get the submitted fields
  $title = $_POST['title'];
  $image = $_POST['image'];
  $cat  = (int)$_POST['cat'];
  $url  = (string)$_POST['url'];
  $content = $_POST['content'];

  if(of_get_option('wpeddit_pending') == 0){
  $status = 'publish';
  }else{
  $status = 'pending';
  }

    $posttype = 'post';
    $author = $current_user->ID;
    $taxonomy = 'category';

  if($_POST['type'] == 'link'){
    if (!filter_var($url, FILTER_VALIDATE_URL)) { 
      $url = '';
    }
    if (!filter_var($image, FILTER_VALIDATE_URL)) { 
      $image = '';
    }

    
    $my_post = array(
      'post_title' => $title,
      'post_status' => $status,
      'post_type' => $posttype,
      'post_author' => $author,
      'post_content' => '',
       );
                         
    $wid = wp_insert_post( $my_post );
    if($image != ''){
      $file = $image;
      $filename = basename($file);

      $wp_filetype = wp_check_filetype($file, null );
      $attachment = array(
          'post_mime_type' => $wp_filetype['type'],
          'post_title' =>     sanitize_file_name($filename),
          'post_content' => '',
          'post_status' => 'inherit'
      );
      $attach_id = wp_insert_attachment( $attachment, $file, $wid);
      update_post_meta($wid,'_thumbnail_id', $attach_id);
    }
    update_post_meta( $wid, 'wpeddit_out', $url ); 
    update_post_meta( $wid, 'epicredvote', 0);    
    wp_set_post_terms( $wid, $cat, $taxonomy);

  }elseif($_POST['type'] == 'text'){
    
    $my_post = array(
      'post_title' => $title,
      'post_status' => $status,
      'post_type' => $posttype,
      'post_author' => $author,
      'post_content' => $content,
       );
                         
    $wid = wp_insert_post( $my_post );
    update_post_meta( $wid, 'epicredvote', 0);    
    wp_set_post_terms( $wid, $cat, $taxonomy);

  }else{
    die(); //no other type accepted.... 
  }


  $response['success'] = 'post added';
  $response['slug'] = $slug;
  $response['perma'] = get_post_permalink($wid);
  echo json_encode($response); 
  die();

}


function wpedditcats(){
 $args = array(
					    'orderby'            => 'ID', 
					    'order'              => 'ASC',
					    'show_count'         => 0,
					    'hide_empty'         => 0, 
					    'child_of'           => 0,
					    'echo'               => 1,
					    'selected'           => 0,
					    'hierarchical'       => 0, 
					    'name'               => 'cat',
					    'class'              => 'postform',
					    'depth'              => 0,
					    'tab_index'          => 0,
					    'taxonomy'           => 'category',
					    'hide_if_empty'      => false ); 
					
					?>
					 <br/><label>Category</label><br/>
					<?php 
	                  wp_dropdown_categories($args); 
}


function wpsubbed($term_id,$user_id){
  //wrapper for checked if a user is subscribed to a category
  $subs = get_user_meta($user_id,'wpeddit_subs',true);
  $subs_arr = explode(',', $subs);
  $has_subbed = in_array($term_id, $subs_arr);
  return $has_subbed;
}

add_action( 'wp_ajax_nopriv_wpsub', 'wpsub' );
add_action( 'wp_ajax_wpsub', 'wpsub' );
function wpsub(){
  //subs a user to a subwpeddit...
  $term_id = (int)$_POST['wpeddit_term'];
  $current_user = wp_get_current_user();
  //get subscriber count
  $subsc = get_metadata('term', $term_id, 'wpeddit_subs_count',true);
  if($subsc == ''){
    $subsc = 0;
  }
  if($current_user->ID > 0){
     $subs = get_user_meta($current_user->ID,'wpeddit_subs',true);
     $subs_arr = explode(',', $subs);
     if(!in_array($term_id, $subs_arr)){
        array_push($subs_arr, $term_id);
        $subs = implode(',',$subs_arr);
        update_user_meta($current_user->ID,'wpeddit_subs',$subs);
        $subsc_new = $subsc + 1;
        update_metadata ( 'term', $term_id, 'wpeddit_subs_count', $subsc_new, $subsc );
     }
  }
  $r['message'] = 'success'; //for debug
  echo json_encode($r);
  die();   //die the function... ta muchly.
}

add_action( 'wp_ajax_nopriv_wpunsub', 'wpunsub' );
add_action( 'wp_ajax_wpunsub', 'wpunsub' );
function wpunsub(){
  //unsubs a user from a subwpeddit
  $term_id = (int)$_POST['wpeddit_term'];
  $current_user = wp_get_current_user();
  $subsc = get_metadata('term',$term_id,'wpeddit_subs_count',true);
  if($subsc == ''){
    $subsc =0;
    die(); //should never be here... 
  }
  if($current_user->ID >0){
    $subs = get_user_meta($current_user->ID,'wpeddit_subs',true);
    $subs_arr = explode(',',$subs);
    if(($key = array_search($term_id, $subs_arr)) !== false) {
      unset($subs_arr[$key]);    //removes the post from the collected_array();
    }
    $subs = implode(',',$subs_arr);
    update_user_meta($current_user->ID,'wpeddit_subs',$subs);
    $subsc_new = $subsc - 1;
    update_metadata ( 'term', $term_id, 'wpeddit_subs_count', $subsc_new, $subsc );  
  }
  $r['message'] = 'success'; //for debug
  echo json_encode($r);
  die();   //die the function... ta muchly.    
}


function epic_reddit_categories(){
global $wpdb, $wp;

$terms = get_terms( array(
    'taxonomy' => 'category',
    'hide_empty' => false,
) );
?>
<div class='navigation-banner subred-nav'>
  <ul class='post-order'>
    <li><a href='#' class='active' id="popular-sub"><?php _e('new','wpeddit');?></a></li>
    <?php if(is_user_logged_in()){ ?>
    <li><a href='#' id="my-sub"><?php _e('my sub','wpeddit'); ?><?php echo of_get_option('wpeddit_name','wpeddits');?></a></li>
    <?php } ?>
  </ul>
</div>

<div class='wpeddit-alert'>
  <p>click the <span class='code'>subscribe</span> or <span class='code'>unsubscribe</span> buttons to choose which sub<?php echo of_get_option('wpeddit_name','wpeddits');?> appear on your front page.</p>
</div>
<?php
	if ($terms) {
	?>
	<ul class = 'red-more'>
  <div id="wpeddit-popular-sub" class="wpeddit-sub-tab">
	<?php
	  foreach( $terms as $term ) { ?>
        <?php 
        $current_user = wp_get_current_user();
        $cat_base = get_option( 'category_base' );
        if($current_user->ID > 0){
          $wpsub = wpsubbed($term->term_id, $current_user->ID);
        }
        ?>
        <div class="wpeddit-cat-list">
        <div class="wpeddit-marker">
        <?php if(!$wpsub){ ?>
          <div class="wpeddit-sub wpeddit-subscribe" id="wpsub-<?php echo $term->term_id;?>" data-wpsid="<?php echo $term->term_id; ?>"><?php _e("subscribe"); ?></div>
        <?php }else{ ?>
          <div class="wpeddit-sub wpeddit-unsubscribe" id="wpsub-<?php echo $term->term_id;?>" data-wpsid="<?php echo $term->term_id; ?>"><?php _e("unsubscribe"); ?></div>
        <?php } ?>
        </div>
        <div class="wpeddit-info">
        <div class='wpeddit-cat-link'>
        <a href = "<?php echo get_category_link( $term->term_id ); ?>"><?php echo "/" . $cat_base . "/" . $term->name;?></a>
        </div>
        <?php if($term->description != ''){ ?>
          <div class="wpeddit-description">
          <a href = "<?php echo get_category_link( $term->term_id ); ?>"><?php echo "/" . $cat_base . "/" . $term->name;?></a> <?php echo $term->description; ?>
          </div>
        <?php } ?>
        <div class="wpeddit-meta">
        <?php
        $subsc = get_metadata('term', $term->term_id, 'wpeddit_subs_count',true);
        if($subsc == ''){
        $subsc = 0;
        }
        $text = sprintf( _n( '%s subscriber', '%s subscribers', $subsc, 'wpeddit' ), $subsc );
        echo $text;
        ?>
        </div>
        </div>
        </div>
        <div class='clear'></div>
     <?php } ?>
  </div>
  <?php } ?>
  <div id="wpeddit-my-sub" class="wpeddit-sub-tab hide">
    <?php
      //get the users subs
    if(is_user_logged_in()){
      $subs = get_user_meta($current_user->ID,'wpeddit_subs',true);
      $subs_arr = explode(',',$subs);
      foreach($subs_arr as $sub){
        if($sub == ''){
          continue;
        }
      $term = get_term($sub); ?>
        <div class="wpeddit-cat-list" class="wpeddit-sub-tab">
          <div class="wpeddit-marker">
            <div class="wpeddit-sub wpeddit-unsubscribe" id="wpsub-<?php echo $term->term_id;?>" data-wpsid="<?php echo $term->term_id; ?>"><?php _e("unsubscribe"); ?></div>
          </div>
          <div class="wpeddit-info">
            <div class='wpeddit-cat-link'>
              <a href = "<?php echo get_category_link( $term->term_id ); ?>"><?php echo "/" . $cat_base . "/" . $term->name;?></a>
            </div>
          <?php if($term->description != ''){ ?>
            <div class="wpeddit-description">
              <a href = "<?php echo get_category_link( $term->term_id ); ?>"><?php echo "/" . $cat_base . "/" . $term->name;?></a> <?php echo $term->description; ?>
            </div>
          <?php } ?>
            <div class="wpeddit-meta">
              <?php
              $subsc = get_metadata('term', $term->term_id, 'wpeddit_subs_count',true);
              if($subsc == ''){
                $subsc = 0;
              }
              $text = sprintf( _n( '%s subscriber', '%s subscribers', $subsc, 'wpeddit' ), $subsc );
              echo $text;
              ?>
            </div>
          </div>
        </div>
        <div class='clear'></div>
        <?php
      }
    }
    ?>
  </div>
	</ul>
	<?php 
} 
add_shortcode("wpedditmorecats", "epic_reddit_categories");


function epic_theme_settings(){
    global $wpdb;  #} Req
    
    $wpeddit = array();
    $wpeddit['wpeddit'] 		    =           get_option('wpedditstyle'); 
	$wpeddit['wpmenu'] 		  		=           get_option('wpmenu');     
	$wpeddit['wplogo'] 		  		=           get_option('wplogo'); 

    
    ?>
    
<div class = 'wrap'>

	
     <form action="?page=epic-theme-settings&save=1" method="post">
     <div class="postbox">
     <h3><label>General settings</label></h3>
     
     <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
         

        <?php
        //settings array
		$wpstylearray = array('default','bootstrap','light','dark');
		$wpmenuarray = array('fixed','floating');
		
        $i = 0;
        ?>
        <tr valign="top">
        	<td width="25%" align="left"><strong>Theme stylesheet:</strong></td>
			<td align="left">
                <select id= 'wpedditstyle' name = 'wpedditstyle'>
                	<?php foreach ($wpstylearray as $wpstylea){
                		if($wpstylea == $wpeddit['wpeddit'] ){
                		echo "<option value = '$wpstylea' selected>$wpstylea</option>";	
                		}else{
                		echo "<option value = '$wpstylea'>$wpstylea</option>";
						}
						$i++;
					}
					?>
                </select><br><i>What style would you like for your site</i>
        	</td>
        </tr>
        
         <tr valign="top">
        	<td width="25%" align="left"><strong>Menu style:</strong></td>
			<td align="left">
                <select id= 'wpmenu' name = 'wpmenu'>
                	<?php foreach ($wpmenuarray as $wpmenu){
                		if($wpmenu == $wpeddit['wpmenu'] ){
                		echo "<option value = '$wpmenu' selected>$wpmenu</option>";	
                		}else{
                		echo "<option value = '$wpmenu'>$wpmenu</option>";
						}
						$i++;
					}
					?>
                </select><br><i>Fixed or floating menu? (fixed to the top of the page)</i>
        	</td>
        </tr>
        
        <tr valign="top">
        	<td width="25%" align="left"><strong>Logo URL:</strong></td>
			<td align="left">
               	<input type="text" name="logo" id="logo" value = "<?php echo $wpeddit['wplogo'];?>"><br><i>Only shown if floating menu is chosen</i>
        	</td>
        </tr>

      
    </table>
    <p id="footerSub"><input class = "button-primary" type="submit" value="Save settings" /></p>
    </form>
</div>

</div>

<?php
}

#} Save options changes
function epic_theme_save_settings(){
    
    global $wpdb;  #} Req
    
    $wpeddit= array();
    $wpeddit['wpedditstyle'] = $_POST['wpedditstyle'];
	$wpeddit['wpmenu'] = $_POST['wpmenu'];
	$wpeddit['wplogo'] = $_POST['logo'];
    
    #} Save down
    update_option("wpedditstyle", $wpeddit['wpedditstyle']);
	update_option("wpmenu", $wpeddit['wpmenu']);
	update_option("wplogo", $wpeddit['wplogo']);


    #} Msg
    ?>

	<div id="message" class="updated fade below-h2"><strong>Success!</strong> Settings Saved.</div>

    <?php
    #} Run standard
    epic_theme_settings();
    
}

function wpeddit_thumbnail(){
  if ( has_post_thumbnail() ) {
      the_post_thumbnail('thumbnail');
  }else{
    $default = of_get_option('wpeddit_no_image','http://placehold.it/70x70');
    echo "<img class='wp-post-image' src='".$default."' />";
  }
}

//code for changing the custom post order
function wpeddit_order_posts($query) {
    global $wp_query;
    if ( ! $query->is_main_query() )
      return $query;
    
        if(!is_page() || !is_admin()){
            $query->set('meta_key', 'epicredrank');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
            $query->set('ignore_sticky_posts','0');
        }
    return $query;
}
//add_action('pre_get_posts','wpeddit_order_posts');


function wpeddit_sticky(){
      $args = array(
        'posts_per_page' => -1,
        'post__in'  => get_option( 'sticky_posts' ),
        'ignore_sticky_posts' => 1
      );
      $query = new WP_Query( $args );
      foreach($query->posts as $wpstick){
        ?>
        <article class='post sticky'>
          <?php epic_reddit_voting(); ?>
          <header class="entry-header">
            <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
          </header><!-- .entry-header -->
          <footer class="entry-footer">
            <?php // wpeddit_entry_meta(); ?>
            <?php
              edit_post_link(
                sprintf(
                  /* translators: %s: Name of current post */
                  __( 'Edit<span class="screen-reader-text"> "%s"</span>', 'wpeddit' ),
                  get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
              );
            ?>
          </footer><!-- .entry-footer -->
        </article><!-- #post-## -->
      <?php 
      }
  wp_reset_query();
}

function wpeddit_prettyprint($array){
  echo '<pre>';
  var_dump($array);
  echo '</pre>';
}

function epic_scripts_with_jquery() {
 // Register the script like this for a theme: 
wp_register_script( 'epic-script', get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ) ); 
wp_enqueue_script( 'epic-script' );


    wp_enqueue_script("jquery");
    wp_enqueue_script( 'jquery-form',array('jquery')); 
    wp_enqueue_script('epicred-ajax', get_template_directory_uri() . '/js/epicred.js',array('jquery'));
    wp_localize_script( 'epicred-ajax', 'EpicAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
  
  if(!is_admin()){
    wp_enqueue_style('epicred-css', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style('epicred-css', get_template_directory_uri() . '/epicred.css' );
    wp_enqueue_style('epicred_font_a', get_template_directory_uri() . '/css/font-awesome.min.css' );
    wp_enqueue_style('epicred_boot', get_template_directory_uri() . '/css/bootstrap.min.css' );
  }
    
// Moving in the scripts from wpeddit (normal edition)


} 

add_action( 'wp_enqueue_scripts', 'epic_scripts_with_jquery' );

add_action( 'after_setup_theme', 'epic_register_my_menus' );
 
function epic_register_my_menus() {
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'wpeddit' ),
	) );
}



add_filter('show_admin_bar', '__return_false');

register_sidebar(array(
  'name' => __( 'Right Hand Sidebar' ),
  'id' => 'right-sidebar',
  'description' => __( 'Widgets in this area will be shown on the right-hand side.' ),
  'before_title' => '<p class = "title">',
  'after_title' => '</p>'
));

register_sidebar(array(
  'name' => __( 'Footer 1' ),
  'id' => 'footer-sidebar',
  'description' => __( 'Widgets in this area will be shown in the footer 1 location.' ),
  'before_title' => '<h2>',
  'after_title' => '</h2>'
));

register_sidebar(array(
  'name' => __( 'Footer 2' ),
  'id' => 'footer2-sidebar',
  'description' => __( 'Widgets in this area will be shown in the footer 2 location.' ),
  'before_title' => '<h2>',
  'after_title' => '</h2>'
));

register_sidebar(array(
  'name' => __( 'Footer 3' ),
  'id' => 'footer3-sidebar',
  'description' => __( 'Widgets in this area will be shown in the footer 3 location.' ),
  'before_title' => '<h2>',
  'after_title' => '</h2>'
));

register_sidebar(array(
  'name' => __( 'Footer 4' ),
  'id' => 'footer4-sidebar',
  'description' => __( 'Widgets in this area will be shown in the footer 4 location.' ),
  'before_title' => '<h2>',
  'after_title' => '</h2>'
));


function epic_find_cat(){
	 		
			$category_id = get_query_var('cat');
			return   $category_id;
}

 class Bootstrap_Walker extends Walker_Nav_Menu 
    {     
     
        /* Start of the <ul> 
         * 
         * Note on $depth: Counterintuitively, $depth here means the "depth right before we start this menu".  
         *                   So basically add one to what you'd expect it to be 
         */         
        function start_lvl(&$output, $depth = 0, $args = array()) 
        {
            $tabs = str_repeat("\t", $depth); 
            // If we are about to start the first submenu, we need to give it a dropdown-menu class 
            if ($depth == 0 || $depth == 1) { //really, level-1 or level-2, because $depth is misleading here (see note above) 
                $output .= "\n{$tabs}<ul class=\"dropdown-menu\">\n"; 
            } else { 
                $output .= "\n{$tabs}<ul>\n"; 
            } 
            return;
        } 
         
        /* End of the <ul> 
         * 
         * Note on $depth: Counterintuitively, $depth here means the "depth right before we start this menu".  
         *                   So basically add one to what you'd expect it to be 
         */         
        function end_lvl(&$output, $depth = 0, $args = array())  
        {
            if ($depth == 0) { // This is actually the end of the level-1 submenu ($depth is misleading here too!) 
                 
                // we don't have anything special for Bootstrap, so we'll just leave an HTML comment for now 
                $output .= '<!--.dropdown-->'; 
            } 
            $tabs = str_repeat("\t", $depth); 
            $output .= "\n{$tabs}</ul>\n"; 
            return; 
        }
                 
        /* Output the <li> and the containing <a> 
         * Note: $depth is "correct" at this level 
         */         
        function start_el(&$output, $item, $depth = 0, $args = array(), $id=0)  
        {    
            global $wp_query; 
            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : ''; 
            $class_names = $value = ''; 
            $classes = empty( $item->classes ) ? array() : (array) $item->classes; 

            /* If this item has a dropdown menu, add the 'dropdown' class for Bootstrap */ 
            if ($item->hasChildren) { 
                $classes[] = 'dropdown'; 
                // level-1 menus also need the 'dropdown-submenu' class 
                if($depth == 1) { 
                    $classes[] = 'dropdown-submenu'; 
                } 
            } 

            /* This is the stock Wordpress code that builds the <li> with all of its attributes */ 
            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ); 
            $class_names = ' class="' . esc_attr( $class_names ) . '"'; 
            $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';             
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : ''; 
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : ''; 
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : ''; 
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : ''; 
            $item_output = $args->before; 
                         
            /* If this item has a dropdown menu, make clicking on this link toggle it */ 
            if ($item->hasChildren && $depth == 0) { 
                $item_output .= '<a'. $attributes .' class="dropdown-toggle" data-toggle="dropdown">'; 
            } else { 
                $item_output .= '<a'. $attributes .'>'; 
            } 
             
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after; 

            /* Output the actual caret for the user to click on to toggle the menu */             
            if ($item->hasChildren && $depth == 0) { 
                $item_output .= '<b class="caret"></b></a>'; 
            } else { 
                $item_output .= '</a>'; 
            } 

            $item_output .= $args->after; 
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args ); 
            return; 
        }
        
        /* Close the <li> 
         * Note: the <a> is already closed 
         * Note 2: $depth is "correct" at this level 
         */         
        function end_el (&$output, $item, $depth = 0, $args = array())
        {
            $output .= '</li>'; 
            return;
        } 
         
        /* Add a 'hasChildren' property to the item 
         * Code from: http://wordpress.org/support/topic/how-do-i-know-if-a-menu-item-has-children-or-is-a-leaf#post-3139633  
         */ 
        function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) 
        { 
            // check whether this item has children, and set $item->hasChildren accordingly 
            $element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]); 

            // continue with normal behavior 
            return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output); 
        }         
    } 


  function epic_posts_nav() {

  global $wp_query;

  /** Stop execution if there's only 1 page */
  if( $wp_query->max_num_pages <= 1 )
    return;

  $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
  $max   = intval( $wp_query->max_num_pages );

  /** Add current page to the array */
  if ( $paged >= 1 )
    $links[] = $paged;

  /** Add the pages around the current page to the array */
  if ( $paged >= 3 ) {
    $links[] = $paged - 1;
    $links[] = $paged - 2;
  }

  if ( ( $paged + 2 ) <= $max ) {
    $links[] = $paged + 2;
    $links[] = $paged + 1;
  }

  echo '<div class="navigation" id="nav-below"><ul>' . "\n";

  /** Previous Post Link */
  if ( get_previous_posts_link() )
    printf( '<li>%s</li>' . "\n", get_previous_posts_link() );

  /** Link to first page, plus ellipses if necessary */
  if ( ! in_array( 1, $links ) ) {
    $class = 1 == $paged ? ' class="active"' : '';

    printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

    if ( ! in_array( 2, $links ) )
      echo '<li>…</li>';
  }

  /** Link to current page, plus 2 pages in either direction if necessary */
  sort( $links );
  foreach ( (array) $links as $link ) {
    $class = $paged == $link ? ' class="active"' : '';
    printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
  }

  /** Link to last page, plus ellipses if necessary */
  if ( ! in_array( $max, $links ) ) {
    if ( ! in_array( $max - 1, $links ) )
      echo '<li>…</li>' . "\n";

    $class = $paged == $max ? ' class="active"' : '';
    printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
  }

  /** Next Post Link */
  if ( get_next_posts_link() )
    printf( '<li>%s</li>' . "\n", get_next_posts_link() );

  echo '</ul></div>' . "\n";

}



/******
WPEDDIT PLUGIN STUFF 
*****/

  #} Install/uninstall
    register_activation_hook(__FILE__,'wpeddit__install');
    register_deactivation_hook(__FILE__,'wpeddit__uninstall');
    
  #} Initial Vars
  global $epicred_db_version;
  $epicred_db_version                  = "1.0";
  $epicred_version                     = "2.5";
  $epicred_activation                    = '';


  #} Urls
    global $epicred_urls;
    $epicred_urls['home']          = 'http://wpeddit.com/';
    $epicred_urls['docs']          = plugins_url('/documentation/index.html',__FILE__);
  $epicred_urls['forum']         = 'http://forums.epicplugins.com/';
    $epicred_urls['updateCheck']   = 'http://www.epicplugins.com/api/';
  $epicred_urls['regCheck']    = 'http://www.epicplugins.com/registration/';
  $epicred_urls['subscribe']     = "http://eepurl.com/tW_t9";
  
  #} Page slugs
    global $epicred_slugs;
    $epicred_slugs['config']           = "epicred-plugin-config";
    $epicred_slugs['settings']         = "epicred-plugin-settings";

  #} Install function
  function wpeddit__install(){

    #} Default Options

    add_option('epicred_ip','yes','','yes');

  wpeddit_install();
  add_option('wpedditshared','no','','yes'); 
    
  $current_user = wp_get_current_user();    //email the current user rather than admin info more likely to reach a human email 
  $userEmail = $current_user->user_email;
  $userName =  $current_user->user_firstname;
  $LastName =  $current_user->user_lastname;
  $plugin = 'WPeddit';
      
  }
  
  
  global $epicred_db_version;
  $epicred_db_version = "1.0";

   function wpeddit_install() {
   global $wpdb;
   global $epicred_db_version;

   $table_name = $wpdb->prefix . "epicred";
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    epicred_id mediumint(9) NOT NULL,
    epicred_option mediumint(9) NOT NULL,
    epicred_ip text NOT NULL,
    UNIQUE KEY id (id)
      );";

     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
     dbDelta($sql);
   
      $table_name = $wpdb->prefix . "epicred_comment";
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    epicred_id mediumint(9) NOT NULL,
    epicred_option mediumint(9) NOT NULL,
    epicred_ip text NOT NULL,
    UNIQUE KEY id (id)
      );";

     dbDelta($sql);
   
     add_option("epicred_db_version", $epicred_db_version);
     


    }



add_action( 'wp_ajax_nopriv_epicred_vote', 'wpeddit_vote' );
add_action( 'wp_ajax_epicred_vote', 'wpeddit_vote' );

function wpeddit_vote(){
  global $wpdb, $current_user;
  
    wp_get_current_user();
  
  $wpdb->myo_ip   = $wpdb->prefix . 'epicred';
    
  $option = (int)$_POST['option'];
  $current = (int)$_POST['current'];
  
  //if we are locked via IP set the fid variable to be the IP address, otherwise log the member ID
  if(get_option('epicred_ip') == 'yes'){
    $ipAddr = isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) ? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
    $fid = "'" . $ipAddr . "'"; 
  }else{
    $fid = $current_user->ID;
  }

  
  $postid = (int)$_POST['poll'];  
  
  $query = "SELECT epicred_option FROM $wpdb->myo_ip WHERE epicred_ip = $fid AND epicred_id = $postid";
  
  $al = $wpdb->get_var($query);
    
  
  if($al == NULL){
    $query = "INSERT INTO $wpdb->myo_ip ( epicred_id , epicred_ip, epicred_option) VALUES ( $postid, $fid, $option)";
    $wpdb->query($query);
  }else{
    $query = "UPDATE $wpdb->myo_ip SET epicred_option = $option WHERE epicred_ip = $fid AND epicred_id = $postid";
    $wpdb->query($query);
  }
  
    $vote = get_post_meta($postid,'epicredvote',true);
  
    if($option == 1){
      if($al != 1){
        if($al == -1){
        $vote = $vote+2;  
        }else{
        $vote = $vote+1;
        }
      }
    }


    if($option == -1){
      if($al != -1){
        if($al == 1){
          $vote = $vote-2;
        }else{
        $vote = $vote-1;
        } 
      } 
    }


    //Karma for the author... 
    $karmapost = get_post($postid);
    $karmaauthor = $karmapost->post_author;
    //only give karma if OTHERS upvote your posts.. 
    if($karmaauthor != $current_user->ID && $al == NULL){
      $karma = get_user_meta($karmaauthor,'wpeddit_post_karma',true);
      if($karma == ''){
        $karma = 0;
      }
      $newkarma = $karma + $option; //up votes gives a +1 karma, downvotes gives a -1 karma..
      update_user_meta( $karmaauthor, 'wpeddit_post_karma', $newkarma, $karma);
    }
    update_post_meta($postid,'epicredvote',$vote);

    //OK so a vote has happened, however epicredvote is the net vote ('top') will be most upvotes.. ('hot') the best (formula)
    if($al == NULL){   //stops users gaming the system... controversy a lot of UNIQUE up/downs.. 
      $wpeddit_contro = get_post_meta($postid,'wpeddit_contro', true);
      if($wpeddit_contro == ''){
        $wpeddit_contro = 0;
      }
      $wpeddit_contro++;   //there's been some activity... either an up or down (hence controversial if a LOT of ups and downs)
      update_post_meta($postid,'wpeddit_contro', $wpeddit_contro);
    }


    //rising...  will do something like Social Buzz and then store the SUM of the last 50 directions
    if($al == NULL){
      $rising = get_post_meta($posid,'wpeddit_rising_arr',true);
      $rising_arr = explode(',',$rising);
      if($rising == ''){
         $rising_arr = array_fill(0, 50, 0);
      }
      $rising_arr = array_pop($rising_arr);
      $rising_arr = array_push($rising_arr, $option);
      $rising_total = array_sum($rising_arr);
      update_post_meta($postid,'wpeddit_rising',$rising_total);
      $rising = implode(',',$rising_arr);
      update_post_meta($postid,'wpeddit_rising_arr',$rising);
    }
    $response['poll'] = $postid;
    $response['vote'] = $vote;
    
    echo json_encode($response);
  
  // IMPORTANT: don't forget to "exit"
  exit;
}


function wpeddit_hot_comments($posts){
  global $wp, $wp_query,$post,$wpdb, $current_user,$query_string;
    $args = array(
        'meta_key' => 'wpeddit_comment_rank',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'number' => $posts
    );
  $comments_query = new WP_Comment_Query;
  $comments = $comments_query->query( $args );
  ?>
  <ul>
  <?php
  // Comment Loop
  if ( $comments ) {
    foreach ( $comments as $comment ) {
      $ID = $comment->comment_post_ID;
      $permalink = get_comment_link( $comment );  
    ?>
      <li><?php echo comment_excerpt($comment->comment_ID); ?> on <a href="<?php echo $permalink; ?>" title=" <?php echo get_the_title($ID); ?>"><?php echo get_the_title($ID); ?></a></li>
  <?php } ?>
  </ul>
  <?php
  } else {
    echo 'No comments found.';
  }
}

function epic_reddit_voting($post_id){
  global $wp_query,$post,$wpdb, $current_user,$query_string;
    wp_get_current_user();
  $wpdb->myo_ip   = $wpdb->prefix . 'epicred';
      
  $postvote = get_post_meta($post->ID, 'epicredvote' ,true);
      if($postvote == NULL){
        $postvote = 0;
  }
      
      if(isset($post_id)){
        $post->ID = $post_id;
      }

      $fid = $current_user->ID;
      if($fid == ''){
        $fid = 0;
      }      
      $query = "SELECT epicred_option FROM $wpdb->myo_ip WHERE epicred_ip = $fid AND epicred_id = $post->ID";
      $al = $wpdb->get_var($query);
      if($al == NULL){
        $al = 0;
      }
      if($al == 1){
        $redclassu = 'upmod';
        $redclassd = 'down';
        $redscore = 'likes';
      }elseif($al == -1){
        $redclassd = 'downmod';
        $redclassu = 'up';
        $redscore = "dislikes";
      }else{
        $redclassu = "up";
        $redclassd = "down";
        $redscore = "unvoted";
      }
      
       ?>
      
      
      <?php wpeddit_post_ranking($post->ID); ?>

      
      <?php if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      <div class = 'logged-in-only'>
      
      <?php } ?>
      

      <div class = 'reddit-voting'>
        <ul class="unstyled">
      <?php  if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
          <div class="arrow2 <?php echo $redclassu;?> arrow-up-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "up" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <div class="score2 <?php echo $redscore;?> score-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?>><?php echo $postvote; ?></div>
          <div class="arrow2 <?php echo $redclassd;?> arrow-down-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "down" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <?php }else{ ?>
          <div class="arrow <?php echo $redclassu;?> arrow-up-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "up" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <div class="score <?php echo $redscore;?> score-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?>><?php echo $postvote; ?></div>
          <div class="arrow <?php echo $redclassd;?> arrow-down-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "down" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>  
          <?php }  ?>
        </ul>
      </div>  
      <?php  if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      </div>
      <?php } 
  
}



function wpeddit_theme_post_ranking($post_id){
  
  $x = get_post_meta($post_id, 'epicredvote', true );
  if($x == ""){
    $x = 0;
  }
  
  $ts = get_the_time("U",$post_id);
  
  if($x > 0){
    $y = 1;
  }elseif($x<0){
    $y = -1;
  }else{
    $y = 0;
  }
  
  $absx = abs($x);
  if($absx >= 1){
    $z = $absx;
  }else{
    $z = 1;
  }
  
  
  $rating = log10($z) + (($y * $ts)/45000);
  
  update_post_meta($post_id,'epicredrank',$rating);
  
  return $rating;
  
}


#} Widgets
// Discussion widget for discussions sidebar  5.3+
add_action('widgets_init',
  create_function('', 'return register_widget("wpeddit_theme_hot_comment_widget");')
);
class wpeddit_theme_hot_comment_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
      'wpeddit_hot_comments', // Base ID
      __( 'Hot Comments', 'wpeddit' ), // Name
      array( 'description' => __( 'WPeddit Widget: Show your sites hottest comments', 'wpeddit' ), ) // Args
    );
  }
  public function widget( $args, $instance ) {
       global $wp_query,$paged,$post,$wp,$wpdb;
  
        extract($args);$mode = (is_numeric($instance['mode']) ? (int)$instance['mode'] : 5);if (!empty($instance['target'])) $targetStr = ' target="'.$instance['target'].'"'; else $targetStr = '';
    if (!empty($instance['title'])) $title = $instance['title']; else $title = '';    
    if (!empty($instance['item'])) $item = $instance['item']; else $item = '';    
    
    $title = apply_filters( 'widget_title', $instance['title'] );
      
    echo $before_widget;
    
    echo "<h3 class = 'widget-title'>" . $title . "</h4>";
    
    wpeddit_hot_comments($mode);    

    echo $after_widget;
  }
  public function form( $instance ) {
        if (isset($instance['mode'])) $mode = esc_attr($instance['mode']); else $mode = 1;
        if (isset($instance['target'])) $target = esc_attr($instance['target']); else $target = '';
        if (isset($instance['buttontext'])) $buttontext = esc_attr($instance['buttontext']); else $buttontext = ''; ?>
          <p>

                <label for="<?php echo $this->get_field_id('mode'); ?>">
                    Title
                </label>
                <input type = 'text' id="<?php echo $this->get_field_id('title'); ?>" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" value = "<?php echo $instance['title']; ?>">
                
                
                <label for="<?php echo $this->get_field_id('mode'); ?>">
                    Comments to show
                </label>
                <input type = 'text' id="<?php echo $this->get_field_id('mode'); ?>" class="widefat" name="<?php echo $this->get_field_name('mode'); ?>" value = "<?php echo $instance['mode']; ?>">
                
            </p>
          <?php
  }
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
  }
} 






add_action('widgets_init', 'wpeddit_theme_widgets');
function wpeddit_theme_widgets() {
    register_widget('wpeddit_theme_hot_widget');
}


function wpt_save_wpeddit() {

     global $wp, $wpdb, $post;  

    
    // unhook this function so it doesn't loop infinitely
  remove_action('save_post', 'wpt_save_wpeddit');

  
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;
    // OK, we're authenticated: we need to find and save the data. Make sure we don't add an image every time the post is
    // saved as a draft - so keep track of the external URL in a custom field.
    
    
      $vote = get_post_meta($post->ID,'epicredvote',true);
    $rank = get_post_meta($post->ID,'epicredrank',true);
    
    if($vote == ""){
      $vote = 0;
    }
    if($rank == ""){
      $rank = 0;
    }

    update_post_meta($post->ID, 'epicredvote', $vote );
    update_post_meta($post->ID,'epicredrank',$rank);
    

    add_action('save_post', 'wpt_save_wpeddit');
   
}
add_action('save_post', 'wpt_save_wpeddit', 1, 2); // save the custom fields and set the image.

#} version 1.6 adding comments meta ranking work

function wpeddit_theme_custom_comment_field( $comment_id ) {
  //add the comment meta for the ranking algorithm.
   add_comment_meta( $comment_id, 'wpeddit_comment_up', 0 );
   add_comment_meta( $comment_id, 'wpeddit_comment_down', 0 );
   add_comment_meta( $comment_id, 'wpeddit_comment_rank', 0 );
   add_comment_meta( $comment_id, 'wpeddit_comment_votes', 0 );
}
add_action( 'comment_post', 'wpeddit_theme_custom_comment_field' );

function wpeddit_theme_comment_ranking($comment_id){
  $ups    =   get_comment_meta($comment_id,'wpeddit_comment_up',true);
  $downs    =   get_comment_meta($comment_id,'wpeddit_comment_down',true);
    $n = $ups + $downs;
    if($n == 0){
        return 0;
  }else{
    $z = 1.0;
    $phat = $ups / $n;
    $rating = sqrt($phat+$z*$z/(2*$n)-$z*(($phat*(1-$phat)+$z*$z/(4*$n))/$n))/(1+$z*$z/$n);
  } 
  update_comment_meta($comment_id,'wpeddit_comment_rank',$rating);
  return $rating;
}

add_action( 'wp_ajax_nopriv_epicred_vote_comment', 'epicred_theme_vote_comment' );
add_action( 'wp_ajax_epicred_vote_comment', 'epicred_theme_vote_comment' );

function epicred_theme_vote_comment(){
  global $wpdb, $current_user;
  
    wp_get_current_user();
  
  $wpdb->myo_ip   = $wpdb->prefix . 'epicred_comment';
    
    $option = (int)$_POST['option'];
  $current = (int)$_POST['current'];
  $postid = (int)$_POST['poll'];  
    
  //if we are locked via IP set the fid variable to be the IP address, otherwise log the member ID
  if(get_option('epicred_ip') == 'yes'){
    $ipAddr = isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) ? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
    $fid = "'" . $ipAddr . "'"; 
  }else{
    $fid = $current_user->ID;
  }
  
  $query = "SELECT epicred_option FROM $wpdb->myo_ip WHERE epicred_ip = $fid AND epicred_id = $postid";
  
  $al = $wpdb->get_var($query);
    
  
  if($al == NULL){
    $query = "INSERT INTO $wpdb->myo_ip ( epicred_id , epicred_ip, epicred_option) VALUES ( $postid, $fid, $option)";
    $wpdb->query($query);
  }else{
    $query = "UPDATE $wpdb->myo_ip SET epicred_option = $option WHERE epicred_ip = $fid AND epicred_id = $postid";
    $wpdb->query($query);
  }
  
  $ups    =   get_comment_meta($postid,'wpeddit_comment_up',true);
  $downs    =   get_comment_meta($postid,'wpeddit_comment_down',true);
  $vote   =   get_comment_meta($postid,'wpeddit_comment_votes',true);
  
    if($option == 1){
      if($al != 1){
        if($al == -1){
        $vote = $vote+2;  
        $downs = $downs - 1;
        $ups = $ups + 1;
        }else{
        $vote = $vote+1;
        $ups = $ups+1;
        }
      }
    }
    
    
    if($option == -1){
      
      if($al != -1){
        if($al == 1){
          $vote = $vote-2;
          $ups = $ups -1;
          $downs = $downs + 1;
        }else{
        $vote = $vote-1;
        $downs = $downs + 1;
        } 
      } 
    }

    //Karma for the author... 
    $karmapost = get_post($postid);
    $karmaauthor = get_comment_author( $postid );
    //only give karma if OTHERS upvote your comments..
    if($karmaauthor != $current_user->ID){
      $karma = get_user_meta($karmaauthor,'wpeddit_comment_karma',true);
      if($karma == ''){
        $karma = 0;
      }
      $newkarma = $karma + $option; //up votes gives a +1 karma, downvotes gives a -1 karma..
      update_user_meta( $karmaauthor, 'wpeddit_comment_karma', $newkarma, $karma);
    }
    update_post_meta($postid,'epicredvote',$vote);


    update_comment_meta($postid,'wpeddit_comment_votes',$vote);
    update_comment_meta($postid,'wpeddit_comment_up',$ups);
    update_comment_meta($postid,'wpeddit_comment_down',$downs);

  
    $response['poll'] = $postid;
    $response['vote'] = $vote;
    
    echo json_encode($response);
  
  // IMPORTANT: don't forget to "exit"
  exit;
}





function epic_reddit_voting_comment($ID){
  global $wp_query,$post,$wpdb, $current_user,$query_string;
    wp_get_current_user();
  $wpdb->myo_ip   = $wpdb->prefix . 'epicred_comment';
      
  $postvote = get_comment_meta($ID, 'wpeddit_comment_votes' ,true);
  
  if($postvote == NULL){
        $postvote = 0;
  }
      
      //again if IP locked set the fid variable to be the IP address.
  if(get_option('epicred_ip') == 'yes'){
    $ipAddr = isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) ? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
    $fid = "'" . $ipAddr . "'"; 
  }else{
    $fid = $current_user->ID;
  }
      
      $query = "SELECT epicred_option FROM $wpdb->myo_ip WHERE epicred_ip = $fid AND epicred_id = $ID";
      $al = $wpdb->get_var($query);
      if($al == NULL){
        $al = 0;
      }
      if($al == 1){
        $redclassu = 'upmod';
        $redclassd = 'down';
        $redscore = 'likes';
      }elseif($al == -1){
        $redclassd = 'downmod';
        $redclassu = 'up';
        $redscore = "dislikes";
      }else{
        $redclassu = "up";
        $redclassd = "down";
        $redscore = "unvoted";
      }
      
       ?>
      
      
      <?php wpeddit_theme_comment_ranking($ID); ?>

      
      <?php if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      <div class = 'logged-in-only'>
      
      <?php } ?>
      

      <div class = 'reddit-voting'>
        <ul class="unstyled">
      <?php  if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
          <div class="arrowc2 <?php echo $redclassu;?> arrowc-up-<?php echo $ID;?>" data-red-current = <?php echo $al;?> data-red-like = "up" data-red-id = "<?php echo $ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <div class="score2 hide <?php echo $redscore;?> scorec-<?php echo $ID;?>" data-red-current = <?php echo $al;?>><?php echo $postvote; ?></div>
          <div class="arrowc2 <?php echo $redclassd;?> arrowc-down-<?php echo $ID;?>" data-red-current = <?php echo $al;?> data-red-like = "down" data-red-id = "<?php echo $ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <?php }else{ ?>
          <div class="arrowc <?php echo $redclassu;?> arrowc-up-<?php echo $ID;?>" data-red-current = <?php echo $al;?> data-red-like = "up" data-red-id = "<?php echo $ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <div class="score hide <?php echo $redscore;?> scorec-<?php echo $ID;?>" data-red-current = <?php echo $al;?>><?php echo $postvote; ?></div>
          <div class="arrowc <?php echo $redclassd;?> arrowc-down-<?php echo $ID;?>" data-red-current = <?php echo $al;?> data-red-like = "down" data-red-id = "<?php echo $ID;?>" role="button" aria-label="upvote" tabindex="0"></div>  
          <?php }  ?>
        </ul>
      </div>  
      <?php  if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      </div>
      <?php } 
  
}

/* Retreve The Value */
function wpeddit_theme_vote($comment) {
  $ID = get_comment_ID();
  $comm = get_comment($ID);
  echo "<div style = 'float:left'>";
  epic_reddit_voting_comment($ID);
  echo "</div>";
return $comment;
 
}
add_filter( 'comment_text', 'wpeddit_theme_vote' );


function wpeddit_theme_hot($posts){
  global $wp_query,$post,$wpdb, $current_user,$query_string;
  wp_reset_query();
  
    $args = array(
        'meta_key' => 'epicredrank',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'posts_per_page' => $posts
    );
  
  query_posts($args);
  
  if ( have_posts() ) : ?>
    <ul>  
    <?php while ( have_posts() ) : the_post(); ?> 
    <li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
    <?php endwhile; ?>
    </ul>
  <?php else: ?> 

  <?php endif; 
}


function wpeddit_theme_index($args){
  global $wp_query,$post,$wpdb, $current_user,$query_string;
  wp_get_current_user();
  $wpdb->myo_ip   = $wpdb->prefix . 'epicred';

    //need to create our own query_posts for the hot and controversial
  if($args == 'hot' || $args['listing'] == 'hot'){
    
  if(!$wp_query) {
    global $wp_query;
    }
    
  if($args['catty'] == ''){
    $cat = get_query_var('cat');
  }else{
    $cat = $args['catty'];
  }

  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;


    $args = array(
        'meta_key' => 'epicredrank',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'paged' => $paged,
        'cat' => $cat
    );

    query_posts( array_merge( $args , $wp_query->query ) );
    
  }else{
  wp_reset_query(); 
  $cat = get_query_var('cat');
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
   
        'paged' => $paged,
        'cat' => $cat,
        
    );

    query_posts( $query_string );

  }
    
  if ( have_posts() ) : ?>
      
      <?php while ( have_posts() ) : the_post(); ?> 
        
      <?php if(is_page()){
        
      }else{
      
       $postvote = get_post_meta($post->ID, 'epicredvote' ,true);

      wpeddit_theme_post_ranking($post->ID);

      if($postvote == NULL){
        $postvote = 0;
      }
      
      //again if IP locked set the fid variable to be the IP address.
  if(get_option('epicred_ip') == 'yes'){
    $fid = "'" . $_SERVER['REMOTE_ADDR'] . "'"; 
  }else{
    $fid = $current_user->ID;
  }
      
      $query = "SELECT epicred_option FROM $wpdb->myo_ip WHERE epicred_ip = $fid AND epicred_id = $post->ID";
      $al = $wpdb->get_var($query);
      if($al == NULL){
        $al = 0;
      }
      if($al == 1){
        $redclassu = 'upmod';
        $redclassd = 'down';
        $redscore = 'likes';
      }elseif($al == -1){
        $redclassd = 'downmod';
        $redclassu = 'up';
        $redscore = "dislikes";
      }else{
        $redclassu = "up";
        $redclassd = "down";
        $redscore = "unvoted";
      }
      
       ?>
      
      <div class = 'row' style = 'margin-bottom:20px'>
      
      
      <?php if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      <script>var loggedin = 'false';</script>
      <?php }else{  ?>
      <script>var loggedin = 'true';</script>
      <?php } ?>
      
      <?php if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      <a href="#myModal" data-toggle="modal">
      
      <?php } ?>
      
      <div class = 'span3'>

      <div class = 'reddit-voting'>
        <ul class="unstyled">
      <?php  if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
          <div class="arrow2 <?php echo $redclassu;?> arrow-up-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "up" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <div class="score2 <?php echo $redscore;?> score-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?>><?php echo $postvote; ?></div>
          <div class="arrow2 <?php echo $redclassd;?> arrow-down-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "down" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <?php }else{ ?>
          <div class="arrow <?php echo $redclassu;?> arrow-up-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "up" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>
          <div class="score <?php echo $redscore;?> score-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?>><?php echo $postvote; ?></div>
          <div class="arrow <?php echo $redclassd;?> arrow-down-<?php echo $post->ID;?>" data-red-current = <?php echo $al;?> data-red-like = "down" data-red-id = "<?php echo $post->ID;?>" role="button" aria-label="upvote" tabindex="0"></div>  
          <?php }  ?>
        </ul>
      </div>  
      <?php  if(!is_user_logged_in() && get_option('epicred_ip') == 'no') { ?>
      </a>
      <?php } ?>
      <?php
      $wpstyleind = of_get_option('wpeddit_layout_style');
      ?>
      <?php if($wpstyleind == '4col'){ ?>
        <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); ?>
        <?php if ( has_post_thumbnail() ) { ?>
          <div class = 'reddit-image pull-left' style = 'width:100%'>
            <img src = "<?php echo $image[0]; ?>" width = "100%" class="img-rounded">
          </div>
        <?php }else{ ?>
          <?php
            $wimage = get_post_meta($post->ID,'wpedditimage',true);
            if($wimage == ''){
              $wimage = "http://placehold.it/150x150";
            }
          ?>
          <div class = 'reddit-image pull-left'>
            <img src = "<?php echo $wimage; ?>" width = "150px" class="img-rounded">
          </div>
      <?php } ?>
      <?php }else{ ?>
        <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
        <?php if ( has_post_thumbnail() ) { ?>
          <div class = 'reddit-image pull-left' style = 'width:180px'>
            <img src = "<?php echo $image[0]; ?>" width = "180px" class="img-rounded">
          </div>
        <?php }else{ ?>
          <?php
            $wimage = get_post_meta($post->ID,'wpedditimage',true);
            if($wimage == ''){
              $wimage = "http://placehold.it/350x150";
            }
          ?>
          <div class = 'reddit-image pull-left' style = 'width:180px'>
            <img src = "<?php echo $wimage; ?>" width = "180px" class="img-rounded">
          </div>
      <?php } } ?>
      
      </div>
      
      <div class = 'span5'>
        <div class = 'reddit-post pull-left'>
        <p class = 'title'><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
        <span class = 'tagline'>submitted <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?> by <?php the_author_posts_link(); ?> in <?php the_category(' '); ?></span> 
          
          <?php if(!is_single()){ ?>
          <p style = "text-align:justify">
          <?php the_excerpt(); ?> 
          </p>
          <?php }else{ ?>
          <?php the_content(); ?> 
          <?php } ?>
                  <a href="<?php comments_link(); ?>">
            <?php comments_number( 'no comments', 'one comment', '% comments' ); ?>. 
        </a>
        </div>
      

      </div>
      
      <div style="clear:both"></div>
      
        <div class = 'span8 pull-right'>
          <?php comments_template(); ?>
        </div>
      
      </div>
      
      <?php } ?>
      
      <?php endwhile; ?>

      <?php else: ?> 
        <p><?php _e('Sorry, no posts matched your criteria.'); ?></p> 
      <?php endif; ?>
  
  
        
      <div class="pagination pagination-centered">
        <?php
        global $wp_query;
        
        $big = 999999999; // need an unlikely integer
        echo paginate_links( array(
        'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
        'format' => '?paged=%#%',
        'show_all' => False,
        'end_size' => 1,
        'mid_size' => 2,
        'prev_next' => True,
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'type' => 'list'
        ) );
        ?>
      </div>
      
      <?php wp_reset_query(); ?>
      
<?php     }



add_filter( 'manage_edit-post_columns', 'wpeddit_theme_post_columns' ) ;

function wpeddit_theme_post_columns( $columns ) {

    $new_columns = array(

    'rating' => __('Ranking', 'WPeddit'),

    );
  
  return array_merge($columns, $new_columns);

}

add_action( 'manage_post_posts_custom_column', 'wpeddit_theme_post_columnsw', 10, 2 );

function wpeddit_theme_post_columnsw( $column, $post_id ) {
    global $post;

    switch( $column ) {
        
        
        case 'rating' :

            /* Get the post meta. */
            echo number_format((double)get_post_meta( $post_id, 'epicredvote', true ),0);

            break;

        /* Just break out of the switch statement for everything else. */
default:
            break;
    }
}

add_filter( 'manage_edit-post_sortable_columns', 'wpeddit_theme_sortable_columns' );

function wpeddit_theme_sortable_columns( $columns ) {

    $columns['rating'] = 'rating';

   
    return $columns;
}


/* Only run our customization on the 'edit.php' page in the admin. */
add_action( 'load-edit.php', 'wpeddit_theme_post_load' );

function wpeddit_theme_post_load() {
    add_filter( 'request', 'wpeddit_theme_sort_post' );
}

/* Sorts the pics. */
function wpeddit_theme_sort_post( $vars ) {

    /* Check if we're viewing the 'picsmash' post type. */
    if ( isset( $vars['post_type'] ) && 'post' == $vars['post_type'] ) {

        /* Check if 'orderby' is set to 'rating'. */
        if ( isset( $vars['orderby'] ) && 'rating' == $vars['orderby'] ) {

            /* Merge the query vars with our custom variables. */
            $vars = array_merge(
                $vars,
                array(
                    'meta_key' => 'epicredvote',
                    'orderby' => 'meta_value_num'
                )
            );
        }
        

    }

    return $vars;
}

#} Widgets
class wpeddit_theme_hot_widget extends WP_Widget {


  function __construct() {
    parent::__construct(
      'wpeddit_hot_posts', // Base ID
      __( 'Hot Posts', 'wpeddit' ), // Name
      array( 'description' => __( 'WPeddit Widget: Show your sites hottest posts', 'pluginhunt' ), ) // Args
    );
  }


    function widget($args, $instance) {
       global $wp_query,$paged,$post,$wp,$wpdb;
  
        extract($args);$mode = (is_numeric($instance['mode']) ? (int)$instance['mode'] : 5);if (!empty($instance['target'])) $targetStr = ' target="'.$instance['target'].'"'; else $targetStr = '';
    if (!empty($instance['title'])) $title = $instance['title']; else $title = '';    
    if (!empty($instance['item'])) $item = $instance['item']; else $item = '';    
    
    $title = apply_filters( 'widget_title', $instance['title'] );
      
    echo $before_widget;
    
    echo "<h4>" . $title . "</h4>";
    
    wpeddit_theme_hot($mode);   

    echo $after_widget;
    
    }
    function update($new_instance, $old_instance) {
        return $new_instance;
    }
  function form($instance) {
        if (isset($instance['mode'])) $mode = esc_attr($instance['mode']); else $mode = 1;
        if (isset($instance['target'])) $target = esc_attr($instance['target']); else $target = '';
        if (isset($instance['buttontext'])) $buttontext = esc_attr($instance['buttontext']); else $buttontext = ''; ?>
          <p>

                <label for="<?php echo $this->get_field_id('mode'); ?>">
                    Title
                </label>
                <input type = 'text' id="<?php echo $this->get_field_id('title'); ?>" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" value = "<?php echo $instance['title']; ?>">
                
                
                <label for="<?php echo $this->get_field_id('mode'); ?>">
                    Posts to show
                </label>
                <input type = 'text' id="<?php echo $this->get_field_id('mode'); ?>" class="widefat" name="<?php echo $this->get_field_name('mode'); ?>" value = "<?php echo $instance['mode']; ?>">
                
            </p>
          <?php
    
    }

}

class weddit_theme_post_widget extends WP_Widget {

  function __construct() {
    parent::__construct(
      'wpeddit_fancy_link', // Base ID
      __( 'Create a fancy link', 'wpeddit' ), // Name
      array( 'description' => __( 'WPeddit Widget: Creates a fancy link (for default theme)', 'wpeddit' ), ) // Args
    );
  }


    function widget($args, $instance) {
       global $wp_query,$paged,$post,$wp,$wpdb;
  
        extract($args);$mode = (is_numeric($instance['mode']) ? (int)$instance['mode'] : 5);if (!empty($instance['target'])) $targetStr = ' target="'.$instance['target'].'"'; else $targetStr = '';
    if (!empty($instance['title'])) $title = $instance['title']; else $title = '';    
    if (!empty($instance['item'])) $item = $instance['item']; else $item = '';    
    
    $title = apply_filters( 'widget_title', $instance['title'] );
      
    echo $before_widget;
    
    echo "<a href = " . $instance['mode'] . " class = 'morelink'>$title</a>";   

    echo $after_widget;
    
    }
    function update($new_instance, $old_instance) {
        return $new_instance;
    }
  function form($instance) {
        if (isset($instance['mode'])) $mode = esc_attr($instance['mode']); else $mode = 1;
        if (isset($instance['target'])) $target = esc_attr($instance['target']); else $target = '';
        if (isset($instance['desc'])) $buttontext = esc_attr($instance['desc']); else $buttontext = ''; ?>
          <p>

                <label for="<?php echo $this->get_field_id('mode'); ?>">
                    Title
                </label>
                <input type = 'text' id="<?php echo $this->get_field_id('title'); ?>" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" value = "<?php echo $instance['title']; ?>">
                
                
                <label for="<?php echo $this->get_field_id('mode'); ?>">
                   Add Post link
                </label>
                <input type = 'text' id="<?php echo $this->get_field_id('mode'); ?>" class="widefat" name="<?php echo $this->get_field_name('mode'); ?>" value = "<?php echo $instance['mode']; ?>">
                <label>

            </p>
          <?php
    
    }

}


function wpeddit_theme_side_menu(){
  // Get the nav menu based on $menu_name (same as 'theme_location' or 'menu' arg to wp_nav_menu)
    // This code based on wp_nav_menu's code to get Menu ID from menu slug

    $menu_name = 'primary';

    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
  $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

  $menu_items = wp_get_nav_menu_items($menu->term_id);

  $menu_list = '<ul id="menu-' . $menu_name . '" class="nav nav-tabs nav-stacked">';

  foreach ( (array) $menu_items as $key => $menu_item ) {
      $title = $menu_item->title;
      $url = $menu_item->url;
      $menu_list .= '<li><a href="' . $url . '">' . $title . '</a></li>';
  }
  $menu_list .= '</ul>';
    } else {
  $menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
    }
   
   echo $menu_list;
  
}

class wpeddit_theme_side_menu_widget extends WP_Widget {
    function wpeddit_side_menu_widget() {
        parent::WP_Widget(false, $name = 'Display the Side Menu', array(
            'description' => 'WPeddit Widget: Displays a vertical menu (for default theme)'
        ));
    }
    function widget($args, $instance) {
       global $wp_query,$paged,$post,$wp,$wpdb;
  
        extract($args);$mode = (is_numeric($instance['mode']) ? (int)$instance['mode'] : 5);if (!empty($instance['target'])) $targetStr = ' target="'.$instance['target'].'"'; else $targetStr = '';
    if (!empty($instance['title'])) $title = $instance['title']; else $title = '';    
    if (!empty($instance['item'])) $item = $instance['item']; else $item = '';    
    
    
      
    echo $before_widget;

    wpeddit_side_menu();

    echo $after_widget;
    
    }
    function update($new_instance, $old_instance) {
        return $new_instance;
    }
  function form($instance) {
        if (isset($instance['mode'])) $mode = esc_attr($instance['mode']); else $mode = 1;
        if (isset($instance['target'])) $target = esc_attr($instance['target']); else $target = '';
        if (isset($instance['desc'])) $buttontext = esc_attr($instance['desc']); else $buttontext = ''; ?>

          <?php
    
    }

}




require_once dirname( __FILE__ ) . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'ph_grid_theme_register_required_plugins' );

function ph_grid_theme_register_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        array(
            'name'      => 'Theme My Login',
            'slug'      => 'theme-my-login',
            'required'  => true,
        ),
        
        array(
            'name'      => 'Hide Admin Bar',
            'slug'      => 'hide-admin-bar-2013',
            'required'  => false,
        ),
    );

    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
            'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

  }


?>