var TapWarpUploadChecker={
	nowchecking: false,
	error_once_reported: false,
	progressCheckerID: -1,
	last_batch_count: 0,
	
	run: function () {
		setInterval(this.ontick.bind(this),1000);
	},

	ontick: function () {
		if (this.nowchecking)
			return;

		this.do_check();
	},
	library_tmpl:
		'<li tabindex="0" '+
			'role="checkbox" '+
			'aria-label="__IMAGE_NAME__" '+
			'aria-checked="false" '+
			'data-id="__ATTACHMENT_ID__" '+
			'class="attachment save-ready">'+
			'<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">'+
				'<div class="thumbnail">'+
					'<div class="centered">'+
						'<img alt="" draggable="false" src="__IMAGE_URL__">'+
					'</div>'+
				'</div>'+
				'<div class="tapwarp-refresh-request"><div>__REFRESH_REQUEST__</div></div>'+
			'</div>'+
			'<button '+
				'tabindex="-1" '+
				'class="button-link check" '+
				'type="button">'+
				'<span class="media-modal-icon"></span>'+
				'<span class="screen-reader-text">Deselect</span>'+
			'</button>'+
		'</li>',
	upload_new_tmpl:
		'<div class="hide-if-no-js" id="media-items">'+
			'<div class="media-item child-of-0 open" id="media-item-o___RANDOM_ID__">'+
				'<img alt="" src="__IMAGE_URL__" class="pinkynail">'+
				'<a '+
					'target="_blank" '+
					'href="__SITE_ROOT_URL__wp-admin/post.php?post=__ATTACHMENT_ID__&amp;action=edit" '+
					'class="edit-attachment">Edit</a>'+
				'<div class="filename new"><span class="title">__IMAGE_NAME__</span></div>'+
			'</div>'+
		'</div>',
	check_progress: function () {
		if (TapWarpUploadChecker.nowchecking)
			jQuery('.tapwarp-loading-image').addClass('loading');
	},
	do_check: function () {
		this.nowchecking=true;
		this.progressCheckerID=setTimeout(this.check_progress,3000);


		if (jQuery('#wp-media-grid > h1 .tapwarp-loading-image').length==0)
			jQuery('#wp-media-grid > h1').append(jQuery('.tapwarp-loading-image').first().clone());

		var pleaserefresh=jQuery('input#tapwarp-please-refresh').val();

		jQuery.ajax({
			cache: false,
			dataType: 'json',
			data: {
				action: 'tapwarp_checkimage'
			},
			url: ajaxurl,
			method: 'POST',
			complete: function (xhr,textStatus) {
				clearTimeout(TapWarpUploadChecker.progressCheckerID);
				if (!TapWarpUploadChecker.last_batch_count)
					jQuery('.tapwarp-loading-image').removeClass('loading');
				TapWarpUploadChecker.nowchecking=false;
			},
			success: function (response) {
				TapWarpUploadChecker.last_batch_count=0;
				if (!response || !response.images)
					return;

				TapWarpUploadChecker.last_batch_count=response.images.length;
				for (var di=0;di<response.images.length;di++)
				{
					data=response.images[di];
					if (data.attachment_id && data.url && data.name)
					{
						var attachments=jQuery('.attachments');
						var mediaitems=jQuery('#media-items');
						var addto=null;
						var tmpl=null;
						if (attachments.length>0)
						{
							var exist=attachments.find('li[data-id='+data.attachment_id+']');
							if (exist.length>0)
								return;

							tmpl=TapWarpUploadChecker.library_tmpl;
							addto=attachments;
						}
						else if (mediaitems.length>0)
						{
							var exist=mediaitems.find('img[src="'+data.url+'"]');
							if (exist.length>0)
								return;

							tmpl=TapWarpUploadChecker.upload_new_tmpl;
							addto=mediaitems;
						}
						if (addto)
						{
							tmpl=tmpl
								.replace(/__IMAGE_NAME__/g,data.name)
								.replace(/__ATTACHMENT_ID__/g,data.attachment_id)
								.replace(/__IMAGE_URL__/g,data.url)
								.replace(/__RANDOM_ID__/g,data.random_id)
								.replace(/__REFRESH_REQUEST__/g,pleaserefresh)
								.replace(/__SITE_ROOT_URL__/g,data.siteroot);
							var thumbnail=jQuery(tmpl);
							addto.prepend(thumbnail);
							jQuery('.no-media').addClass('hidden');
						}
					}
				}

				if (response.errors && response.errors.length>0 && !TapWarpUploadChecker.error_once_reported)
				{
					alert(response.errors.join('\n'));
					TapWarpUploadChecker.error_once_reported=true;
				}
			} // end of success
		});
	}
};

window.addEventListener('load',function () { TapWarpUploadChecker.run(); });
