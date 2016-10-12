</div> <!-- end id = "page" -->


		<div id="myModal" class="modal hide fade">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h6 id="myModalLabel">you'll need to login or register to do that</h6>
		  </div>
		  <div class="modal-body">
		    <div class = "reddit-register pull-left divide">
		    	<?php echo do_shortcode("[theme-my-login default_action='register']"); ?>
		    </div>
		    <div class = "reddit-login pull-right">
		    	<?php echo do_shortcode("[theme-my-login default_action='login']"); ?>
		    </div>
		  </div>
		  <div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		  </div>
		</div>

<?php wp_footer(); ?>
</body>
</html>