<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="login red-login-form" id="theme-my-login<?php $template->the_instance(); ?>">
    <h4 class="modal-title">log in</h4>
    <?php $template->the_action_template_message('login'); ?>
    <?php $template->the_errors(); ?>
    <form name="loginform" id="loginform<?php $template->the_instance(); ?>"
          action="<?php $template->the_action_url('login'); ?>" method="post">
        <div class="c-form-group">
            <input type="text" name="log" id="user_login<?php $template->the_instance(); ?>"
                   placeholder="<?php _e('username', 'theme-my-login') ?>" class="c-form-control input"
                   value="<?php $template->the_posted_value('log'); ?>" size="20"/>
        </div>

        <div class="c-form-group">
            <input type="password" name="pwd" id="user_pass<?php $template->the_instance(); ?>"
                   placeholder="<?php _e('password', 'theme-my-login') ?>" class="c-form-control input" value="" size="20"/>
        </div>
        <?php
        do_action('login_form'); // Wordpress hook
        do_action_ref_array('tml_login_form', array(&$template)); // TML hook
        ?>
        <div class="c-checkbox">
            <input name="rememberme" type="checkbox" id="rememberme<?php $template->the_instance(); ?>"
                   value="forever"/>
            <label for="rememberme<?php $template->the_instance(); ?>"
                   class="rememberme"><?php _e('Remember Me', 'theme-my-login'); ?></label>
            <a href="/lostpassword" class="c-pull-right">reset password</a>
        </div>

        <div class="c-clearfix c-submit-group">
            <input type="submit" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" class="c-btn c-btn-primary c-pull-right"
                   value="<?php _e('Log In', 'theme-my-login'); ?>"/>
            <input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url('login'); ?>"/>
            <input type="hidden" name="testcookie" value="1"/>
            <input type="hidden" name="instance" value="<?php $template->the_instance(); ?>"/>
        </div>
    </form>
    <?php //$template->the_action_links(array('login' => false, 'register' => false)); ?>
</div>

<div style="clear:both"></div>
