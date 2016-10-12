<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="login red-login-form" id="theme-my-login<?php $template->the_instance(); ?>">
	<?php $template->the_action_template_message( 'login' ); ?>
	<?php $template->the_errors(); ?>
	<form name="loginform" id="loginform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'login' ); ?>" method="post">
		<p class = 'red-login'>
			<input type="text" name="log" id="user_login<?php $template->the_instance(); ?>" placeholder = "<?php _e( 'username', 'theme-my-login' ) ?>" class="input" value="<?php $template->the_posted_value( 'log' ); ?>" size="20" />
		</p>
		<p class = 'red-pword'>
			<input type="password" name="pwd" id="user_pass<?php $template->the_instance(); ?>" placeholder = "<?php _e( 'password', 'theme-my-login' ) ?>" class="input" value="" size="20"  />
		</p>
<?php
do_action( 'login_form' ); // Wordpress hook
do_action_ref_array( 'tml_login_form', array( &$template ) ); // TML hook
?>
		<p class="forgetmenot pull-left">
			<input name="rememberme" type="checkbox" id="rememberme<?php $template->the_instance(); ?>" value="forever" />
			<label for="rememberme<?php $template->the_instance(); ?>" class = "rememberme"><?php _e( 'Remember Me', 'theme-my-login' ); ?></label>
		</p>
		<p class="submit  pull-right">
			<input type="submit" class = "btn-mini btn" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" value="<?php _e( 'Log In', 'theme-my-login' ); ?>" />
			<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'login' ); ?>" />
			<input type="hidden" name="testcookie" value="1" />
			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
		</p>
	</form>
	<?php $template->the_action_links( array( 'login' => false ) ); ?>
</div>

<div style = "clear:both"></div>
