<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name() {
	return 'wpeddit-theme';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'wpeddit'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/img/';

	$options = array();

	$options[] = array(
		'name' => __( 'General', 'wpeddit' ),
		'type' => 'heading'
	);


	$options[] = array(
		'name' => __( 'Main logo', 'wpeddit' ),
		'desc' => __( 'What is your main website logo (120px x 40px fits best).', 'wpeddit' ),
		'id' => 'main_logo',
		'type' => 'upload'
	);


	$options[] = array(
		'name' => __( '"More" category link', 'wpeddit' ),
		'desc' => __( 'Enter the link for more categories here.', 'wpeddit' ),
		'id' => 'wpeddit_more_cat',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( 'Your website "wpeddits" string', 'wpeddit' ),
		'desc' => __( 'We call categories "wpeddits" what do you want to name them? These will show under sub[name] too', 'wpeddit' ),
		'id' => 'wpeddit_name',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( 'Default post image', 'wpeddit' ),
		'desc' => __( 'What image should the theme show if no featured image (70px by 70px)', 'wpeddit' ),
		'id' => 'wpeddit_no_image',
		'type' => 'upload'
	);


	$options[] = array(
		'name' => __( 'Options', 'wpeddit' ),
		'type' => 'heading'
	);

	//show full content or tagline input box...
	$options[] = array(
		'name' => __( 'Admin review submissions', 'wpeddit' ),
		'desc' => __( 'Setting this off will make all posts direct to publish', 'wpeddit' ),
		'id' =>'wpeddit_pending',
		'type' => 'checkbox'
	);


	$options[] = array(
		'name' => __( 'Custom CSS', 'wpeddit' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( 'Custom CSS for desktop', 'wpeddit' ),
		'desc' => __( 'Enter your custom CSS for desktop here.', 'wpeddit' ),
		'id' => 'wpeddit_custom_css',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( 'Custom CSS for mobile', 'wpeddit' ),
		'desc' => __( 'Enter your custom CSS for mobile here.', 'wpeddit' ),
		'id' => 'wpeddit_mobile_custom_css',
		'std' => '',
		'type' => 'textarea'
	);


	$options[] = array(
		'name' => __( 'Google Analytics', 'wpeddit' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( 'Tracking code', 'wpeddit' ),
		'desc' => __( 'Enter your analytics tracking code here (without the &#x3C;script&#x3E;&#x3C;/script&#x3E; tags).', 'wpeddit' ),
		'id' => 'wpeddit_ga',
		'std' => '',
		'type' => 'textarea'
	);



	/**
	 * For $settings options see:
	 * http://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * 'media_buttons' are not supported as there is no post to attach items to
	 * 'textarea_name' is set by the 'id' you choose
	 */



	return $options;
}