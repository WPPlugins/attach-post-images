<p class="hide-if-no-js">
	<a title="Attach Images" href="<?php echo $href; ?>" id="twp-attach-post-images-uploader" class="<?php $class; ?>">
		Attach Images
	</a> (use CTRL key to select many)
	<br />
	<input type="hidden" id="twp-attach-post-images-selected" name="selected_post_image" value="<?php echo $images_str; ?>" />
</p>
<div class="hide-if-no-js" id="twp-attach-post-images-list-container">
	<ul id="twp-attach-post-images-list">
		<?php if (!empty($images)) : foreach ($images as $image) : ?>
			<li>
				<img src="<?php echo $image->url; ?>" class="<?php echo $image->orientation; ?>" />
				<a href="javascript:void(0)" class="delete" data-id="<?php echo $image->id; ?>">x</a>
			</li>
		<?php endforeach; endif; ?>
	</ul>
	<div style="clear:both;"></div>
</div>

<script type="text/html" id="twp-attach-post-images-list-item-tpl">
	<li><img src="{src}" class="{class}" /><a href="javascript:void(0)" class="delete" data-id="{id}">x</a></li>
</script>

