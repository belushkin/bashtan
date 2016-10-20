<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="login" id="theme-my-login<?php $template->the_instance(); ?>">
    <h4 class="modal-title">create a new account</h4>
    <?php $template->the_action_template_message('register'); ?>
    <?php $template->the_errors(); ?>
    <form name="registerform" id="registerform<?php $template->the_instance(); ?>"
          action="<?php $template->the_action_url('register'); ?>" method="post">
        <div class="c-form-group">
            <input type="text" placeholder="<?php _e('username', 'theme-my-login') ?>" name="user_login"
                   id="user_login<?php $template->the_instance(); ?>" class="c-form-control input"
                   value="<?php $template->the_posted_value('user_login'); ?>" size="20"/>
        </div>

        <div class="c-form-group">
            <input type="text" placeholder="<?php _e('email', 'theme-my-login') ?>" name="user_email"
                   id="user_email<?php $template->the_instance(); ?>" class="c-form-control input"
                   value="<?php $template->the_posted_value('user_email'); ?>" size="20"/>
        </div>
        <?php
            do_action('register_form'); // Wordpress hook
            do_action_ref_array('tml_register_form', array(&$template)); //TML hook
        ?>

        <div style="clear:both"></div>

        <div class="c-clearfix c-submit-group">
            <input type="submit" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" class="c-btn c-btn-primary c-pull-right"
                   value="<?php _e('Register', 'theme-my-login'); ?>"/>
            <input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url('register'); ?>"/>
            <input type="hidden" name="instance" value="<?php $template->the_instance(); ?>"/>
        </div>
    </form>
    <?php //$template->the_action_links(array('register' => false)); ?>
</div>