<form class = 'form-search' method="get" id="searchform" action="<?php echo esc_url( home_url() ); ?>/">
<div class="input">
	<label class="hidden" for="s" style = "display:none"><?php _e('Search this site:'); ?></label>
	<input type="text" class="input-xlarge" placeholder = 'Search..' value="<?php the_search_query(); ?>" name="s" id="s" />
	<input type="hidden" name="post_type" value="post" />

</div>
</form>
