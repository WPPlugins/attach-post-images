<?php

/**
 * Plugin Class: TWL_API_Post
 *
 * Attach Images to posts
 */

class TWL_API_Post {

	/**
	 * @var TWL_API_Post 
	 */
	protected static $instance = null;

	/**
	 * @var wpdb
	 */
	private $db;

	/**
	 * @var string
	 */
	private $meta_key = '_twp_post_images';

	protected function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * @return TWL_API_Post
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('add_meta_boxes', array($this, 'attachMetaBox'));
		add_action('save_post', array($this, 'savePostImages'));
		add_shortcode('twp_post_images', array($this, 'doShortCode'));
	}

	public function enqueueScripts() {
		/**
		 * Enqueue scripts only if we are in admin and current page is post.php and post-new.php
		 */
		global $pagenow;
		if (is_admin() && strpos($pagenow, 'post') !== false) {
			wp_enqueue_media();
			wp_register_script('twp-attach-post-pages-js', plugins_url('application.js', __FILE__), array('jquery'));
			wp_enqueue_script('twp-attach-post-pages-js');
			wp_enqueue_style('twp-attach-post-pages-css', plugins_url('styles.css', __FILE__));
		}
	}

	public function attachMetaBox() {
		// @todo, save this in some settings
		$screens = array('post', 'page');
		foreach ($screens as $screen) {
			add_meta_box('twp-attach-post-images', __('Attach Images', TWL_API_DIR), array($this, 'attachMetaBoxHTML'), $screen, 'side');
		}
	}

	/**
	 * Print action button and maybe already existing images for this post (add a scroll)
	 *
	 * @param WP_Post $post
	 */
	public function attachMetaBoxHTML($post) {
		wp_nonce_field(TWL_API_DIR, 'twp_post_images_plugin_nonce');
		$images_arr = get_post_meta($post->ID, $this->meta_key, true);
		$images_str = '';
		$images = array();

		if ($images_arr) {
			$images = $this->getImagesFromIds($images_arr);
			$images_str = implode('|', $images_arr);
		}

		$params = array(
			'post_id' => $post->ID,
			'width' => 640,
			'height' => 557,
			'TB_iframe' => 1,
			'type' => 'image',
		);

		$href = admin_url('media-upload.php?' . http_build_query($params));
		$class = 'thickbox';
		include TWL_API_DIR . '/metabox.php';
	}

	public function savePostImages($post_id) {

		// Check if our nonce is set.
		if (!isset($_POST['twp_post_images_plugin_nonce']))
			return $post_id;

		$nonce = $_POST['twp_post_images_plugin_nonce'];

		// Verify that the nonce is valid.
		if (!wp_verify_nonce($nonce, TWL_API_DIR))
			return $post_id;

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;

		// Check the user's permissions.
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id))
				return $post_id;
		} else {
			if (!current_user_can('edit_post', $post_id))
				return $post_id;
		}

		// Sanitize user input.
		$selected_images = sanitize_text_field($_POST['selected_post_image']);
		$image_ids = explode('|', $selected_images);
		foreach ($image_ids as $i => $id) {
			if (!$id) {
				unset($image_ids[$i]);
			}
		}

		// Update the meta field in the database.
		update_post_meta($post_id, $this->meta_key, array_values($image_ids));
	}

	/**
	 * Get a list of images attached to a post
	 *
	 * @param int $post_id The ID of the post for which you want to get attached images
	 * @param string $size Size of the image shown for an image attachment (either of thumbnail, medium, large or full) 
	 * @return array Returns an array (of objects) of the images attached to a post. Returns an empty array if no images were found.
	 * 				 Each object has properties[id, url, width, height, orientation, is_original];
	 */
	public function getPostImages($id, $size = 'thumbnail') {
		$images = array();
		if (!$id) return $images;

		$images_arr = get_post_meta($id, $this->meta_key, true);
		if ($images_arr) {
			$images = $this->getImagesFromIds($images_arr, $size);
		}

		return $images;
	}

	public function doShortCode($params) {
		extract(shortcode_atts(array(
			'id' => 0,
			'size' => 'thumbnail',
		), $params));

		$images = $this->getPostImages($id, $size);
		if (!$images) return;

		$html = '<ul class="twp-post-image twp-post-image-'.$id.'" id="twp-post-image-'.$id.'">';
		foreach ($images as $image) {
			$html .= $this->li($image);
		}
		$html .= '</ul>';
		return $html;
	}

	private function li($image) {
		$template = '<li class="twp-post-image-item twp-post-image-item-{id}"><img src="{url}" class="twp-post-image" width="{width}" height="{height}" /></li>';
		foreach ($image as $k => $v) {
			$template = str_replace('{'.$k.'}', $v, $template);
		}
		return $template;
	}

	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
	 * @todo write an optimized query to get same info in one query
	 */
	private function getImagesFromIds($ids, $size = 'thumbnail') {
		$images = array();

		foreach ($ids as $id) {
			$meta = wp_get_attachment_image_src($id, $size);

			$info = array();
			$info['orientation'] = $meta[1] > $meta[2] ? 'landscape' : 'portrait';
			$info['id'] = $id;
			$info['url'] = $meta[0];
			$info['width'] = $meta[1];
			$info['height'] = $meta[2];
			$info['is_original'] = !$meta[3];
			$images[] = (object) $info;
		}

		return $images;
	}

}