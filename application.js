/**
	Document   : application.js
    Created on : Mar 6, 2014, 5:25:08 PM
    Author     : Cyril Tata
	Requires   : jQuery, wp
    Description: Attach Images to Posts JS admin application. Launches wp media uploder and allows user selects images to attach to post.
*/

(function($, app, undefined) {

	var $list, $select, $selected;
	var uploader;
	var liTemplate;

	app.init = function() {
		$list = $('#twp-attach-post-images-list');
		$select = $('#twp-attach-post-images-uploader');
		$selected = $('#twp-attach-post-images-selected');
		liTemplate = $('#twp-attach-post-images-list-item-tpl').html();
		_bindClickHandlers();
	};

	var _replace = function(string, params) {
		for (var i in params) {
			var t = "\{" + i + "\}";
			string = string.replace((new RegExp(t, 'g')), params[i]);
		}
		return string;
	};

	var _bindClickHandlers = function() {
		$select.bind('click', _launchUploader);
		$list.bind('click', _listClickHandler);
	};

	var _launchUploader = function(event) {
		event.preventDefault();

		// If the uploader object has already been created, reopen the dialog
		if (uploader) {
			uploader.open();
			return;
		}

		// Extend the wp.media object
		uploader = wp.media.frames.files_frame = wp.media({
			title: 'Select post images',
			button: {
				text: 'Attach Selected Images'
			},
			multiple: true,
			type: 'image'
		});

		// Set selected files before opening
		uploader.on('open', function() {
			var selection = uploader.state().get('selection');
			var ids = $selected.val().split('|');
			$.each(ids, function(i, id) {
				var attachment = wp.media.attachment(id);
				attachment.fetch();
				selection.add(attachment ? [attachment] : []);
			});
		});

		// Set selected images
		uploader.on('select', function() {
			$list.html('');
			var ids = [];
			var selected = uploader.state().get('selection').toJSON();
			if (selected.length) {
				$.each(selected, function(e, item) {
					if (item.type == "image") {
						ids.push(item.id);
						_appendImage(item);
					}
				});
				$selected.val(ids.join('|'));
				//@todo maybe save images by ajax
			}
		});

		uploader.open();
	};

	var _appendImage = function(image) {
		var src = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
		var maxWidth = 75;
		var $li = $(_replace(liTemplate, {
			'src': src,
			'class': image.orientation,
			'id': image.id
		}));

		var $image = $li.find('image');

		if (image.orientation == "landscape") {
			var ratio = image.width / image.height;
			var height = maxWidth / ratio;
			var topMargin = Math.round((maxWidth - height) / 2);
			$image.css({marginTop: topMargin});
		}
		$list.prepend($li);
	};

	var _listClickHandler = function(event) {
		var $target = $(event.target);

		var $delete = $target.closest('.delete');
		if ($delete.length && confirm("Are you sure?")) {
			var id = $delete.attr('data-id');
			_deleteImage(id);
			$target.parents('li').remove();
		}
	};

	var _deleteImage = function(did) {
		var ids = $selected.val().split('|');
		var save = [];
		$.each(ids, function(i, id) {
			if (id != did) {
				save.push(id);
			}
		});
		$selected.val(save.join('|'));
	};

	$(document).ready(function() {
		app.init();
	});

}(jQuery, window.TWPapi = window.TWPapi || {}));