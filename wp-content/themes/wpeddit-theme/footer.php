</div> <!-- end id = "page" -->


<div id="myModal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="split-panel">
            <div class="reddit-register pull-left divide">
                <?php echo do_shortcode("[theme-my-login default_action='register-popup' show_title=0]"); ?>
            </div>
            <div class="reddit-login pull-left">
                <?php echo do_shortcode("[theme-my-login default_action='login-popup' show_title=0]"); ?>
            </div>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>