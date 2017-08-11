=== Attach Post Images ===
Contributors: tcyr
Donate link: http://goo.gl/m4r02B
Tags: posts, widget, attach images, post images, attach images to post
Requires at least: 3.5
Tested up to: 3.8.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Attach images to posts (independent of post content) and control post images display.

== Description ==

Unlike the traditional way of attaching images (or attachments) to posts by inserting them in the post content, this plugin allows you to attach images to posts in a manner
that lets you control the way the images are later displayed in your theme.

It adds a metabox to the edit screen that lets you select/upload images (similar to the "Featured Image" metabox).

The images attached to a post can then later be gotten by the following means:

* If you are in a WordPress loop then you can use the tag `twp_the_post_images($size)`. 
*$size (string|array)* is an optional parameter (defaults to '*thumbnail*') and can take values similar to the [wp_get_attachment_image_src](http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src "wp_get_attachment_image_src") function.

This will return an array of objects where each object contains information about an image.

* You can directly call the plugin function `twp_get_post_images($post_id, $size)`.

*$post_id (int)*: required - the ID of the post.

*$size (string|array)*: is an optional parameter (defaults to '*thumbnail*') and can take values similar to the [wp_get_attachment_image_src](http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src "wp_get_attachment_image_src") function.

This will return an array of objects where each object contains information about an image.

* If you want to display the images directly as an unordered list, then you can use the shortcode `[twp_post_images id=post_id size=some_size]`.
The parameters of this shortcode are same as those of the above functions.
You will need some CSS knowledge to style the returned unordered list properly.

The functions `twp_the_post_images()` and `twp_get_post_images()` return an empty array if no images were found or an array of objects where each object has the following attributes:


* *id*: the attachment id
* *width*: The width of the image
* *height*: The height of the image
* *orientation*: The orientation of the image (landscape|protrait)
* *url*: The url of the image
* *is_original*:  (boolean) false if $url is a resized image, true if it is the original.


**NOTE THAT YOU HAVE TO SAVE/UPDATE YOUR POST EACH TIME YOU MODIFY THE IMAGE SELECTION**

== Installation ==

Steps to install this plugin.

1. In the downloaded zip file, there is a folder with name 'attach-post-images'
2. Upload the 'attach-post-images' folder to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Read the usage instructions in the description.

== Screenshots ==

1. Metabox on the right of the edit screen with no images selected
2. Metabox showing selected images

== Frequently Asked Questions ==

= How do I attach images to a post? =

First ensure the plugin is enabled. On the editor screen, there is a meta box on the right with link "Attach Images". This launches the WordPress media manager, where you can select existing images or upload new images.

= How do I get attached images? =

In a WP loop, you can do `$images = twp_the_post_images();`

Or you can call the plugin function `$images = twp_get_post_images($post_id)`. See plugin description for return values.

== Changelog ==

= 1.0 =
* Initial version of plugin

== Upgrade Notice ==

= 1.0.1 =
- rename plugin

= 1.0 =
- fix plugin name
- fix plugin name 2



