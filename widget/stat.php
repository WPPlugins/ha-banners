<?php
$id_banner = (is_numeric($_POST['clickIdBanner']) ? (int)$_POST['clickIdBanner'] : false);

global $wpdb, $haa_banners_prefs_table;
if($id_banner != false){
	$click_view = $wpdb->get_var( $wpdb->prepare( "SELECT img_clicks FROM $haa_banners_prefs_table WHERE id = %d", $id_banner) );
}
if($click_view != ''){
	$sql = $wpdb->update( 
	$haa_banners_prefs_table, 
		array( 
			'img_clicks' => $click_view+1						
		), 
		array( 'id' => $id_banner ),
		array( 
			'%d'					
		), 
		array( '%d' ) 
	);			
	$wpdb->query($sql);
}
?>