jQuery( document ).ready(function() {	
	
	jQuery(".ha_banner").bind('click', function(){
		var clickId = jQuery(this).attr('data-clicks');
			
		jQuery.ajax( {
			type: 'POST',
			url: the_ajax_script.ajaxurl,
			data: 'clickIdBanner=' + clickId
		} );		
		
	});
});