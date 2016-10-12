<div class="reddit-right">
	<?php

	global $author, $username;
	$karma = get_user_meta($author,'wpeddit_post_karma',true);
	if($karma == ''){
		$karma = 0;
	}
	$ckarma = get_user_meta($author,'wpeddit_comment_karma',true);
	if($ckarma == ''){
		$ckarma = 0;
	}
	?>
	<div class='wpeddit-karma'>
		<h2><?php echo $username; ?></h2>
		<div class='karma'>
			<span><?php echo $karma; ?></span><?php _e('post karma','wpeddit'); ?><br/>
			<span><?php echo $ckarma; ?></span><?php _e('comment karma','wpeddit'); ?>
		</div>
	</div>
	
</div>