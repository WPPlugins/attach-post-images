<?php

/**
 * Plugin Name: Attach Post Images
 * Plugin URI: http://cyriltata.blogspot.com/2014/03/wordpress-plugin-attach-post-images.html
 * Description: Attach Images to post (independent of post content). When adding/editing a new post/page, you are able to attach a number of images to a post and get them later using the following methods: 1. Calling the function twp_get_post_images($post_id). 2. In a WP loop using the tag twp_the_post_images(). This returns an array. 3. Using the short code [twp_post_images id=xx]
 * Author: Cyril Tata
 * Version: 1.0.1
 * Author URI: http://cyriltata.blogspot.com
 */

define('TWL_API_FILE', __FILE__);

define('TWL_API_DIR', dirname(TWL_API_FILE));

require_once TWL_API_DIR . '/class.php';

function twp_attach_post_images_activation() { /* nothing to activate here */ }

register_activation_hook(TWL_API_FILE, 'twp_attach_post_images_activation');

/**
 * Get a list of images attached to a post
 * (Attached using the 'Attach Images to post' plugin)
 *
 * @param int $post_id The ID of the post for which you want to get attached images
 * @param string $size Size of the image shown for an image attachment (either of thumbnail, medium, large or full) 
 * @return array Returns an array (of objects) of the images attached to a post. Returns an empty array if no images were found.
 * 				 Each object has properties [id, url, width, height, orientation, is_original];
 */
function twp_get_post_images($post_id = 0, $size = 'thumbnail') {
	if (!$post_id) {
		return array();
	}
	return TWL_API_Post::getInstance()->getPostImages($post_id, $size);
}

/**
 * Use this function in a wordpress loop to get list of images attached to a post
 * (Attached using the 'Attach Images to post' plugin)
 *
 * @param string $size Size of the image shown for an image attachment (either of thumbnail, medium, large or full)
 * @link https://codex.wordpress.org/The_Loop
 * @uses twp_get_post_images
 * @return array
 */
function twp_the_post_images($size = 'thumbnail') {
	$post_id = get_the_ID();
	return twp_get_post_images($post_id, $size);
}

TWL_API_Post::getInstance()->init();