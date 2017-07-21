jQuery( document ).ready(function() {
	var sub_count = jQuery("#sub_count").html();
	var banner_img='';
	var titleimg='';
	
	function reset_banner(){
		jQuery("#haa_banners-form input[type='text'], .p_right input").val('');		
		jQuery("#banner_img").css('display', 'none');		
		jQuery('#img_target').prop("selectedIndex",null);
	}
	
	jQuery('#reset_banner').click(function() {
		reset_banner();
	});
	
	if(sub_count==1){
		reset_banner();
	}
	
	jQuery('#SubmitEdit, #Submit').click(function() {
		banner_img = jQuery("#haa_banners-form #name_img").val();
		titleimg = jQuery("#haa_banners-form #titleimg").val();
			
		if(banner_img==''){
			jQuery(".uploadfile").css('display', 'block');
			return false;
				
		} else if(titleimg==''){
			jQuery(".titleimg").css('display', 'block');
			return false;			
		} else {
			jQuery( "#haa_banners-form" ).submit();
		}
	});
});