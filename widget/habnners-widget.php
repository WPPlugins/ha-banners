<?php
wp_register_style( 'haa_banner-widget-style', plugins_url( 'ha-banners/widget/style.css' ) );
wp_enqueue_style( 'haa_banner-widget-style' );

function haa_banner_widgets() {
    register_widget( 'HAA_Banners' );
}
add_action( 'widgets_init', 'haa_banner_widgets' );

class HAA_Banners extends WP_Widget {
	
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'haa_banners_widgets',
			'description' => __('Widget rotation banners', 'haa_banners_lang'),
		);
		parent::__construct( 'haa_banners_widgets', 'HAA Banners', $widget_ops );
	}
	
	
	
	public function widget ( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );
						
		echo $before_widget;
		if ( ! empty( $title ) ) {
            echo( $before_title . $title . $after_title );
        }
		
		$id_baners_num = sanitize_text_field($instance['ha_banner_ids']);
		if($instance['view']!='') {$limit = ' LIMIT '.(int)$instance['view'];} else {$limit = '';}
		if($instance['rand']!=''){
			$rand = 'ORDER BY RAND()';
		} else {
			$rand = 'ORDER BY FIELD(id, '.sanitize_text_field($instance["ha_banner_ids"]).')';
		}
		
		$view = sanitize_text_field($instance['view']);
		$ban_block_size = '';
						
		global $wpdb, $haa_banners_prefs_table;		
		$haa_banners_view = $wpdb->get_results( "SELECT * FROM $haa_banners_prefs_table WHERE id IN (".$id_baners_num.") ".$rand." ".$limit." " );
		
		if($instance['jquery-rotation']!=''){
			$pieces = explode(", ", $instance["ha_banner_ids"]);
			$counts = count($pieces);
								
			$ban_width = ($instance['jquery-rotation-width']!='' && is_numeric($instance['jquery-rotation-width']) ? (int)$instance['jquery-rotation-width'] : '');
			$ban_height = ($instance['jquery-rotation-height']!='' && is_numeric($instance['jquery-rotation-height']) ? (int)$instance['jquery-rotation-height'] : '');
			$ban_interval = ($instance['jquery-rotation-seconds']!='' && is_numeric($instance['jquery-rotation-seconds']) ? $instance['jquery-rotation-seconds']*1000 : 3000);
			
			if($ban_width != '' && $ban_height != ''){
				$ban_block_size = 'style="width:'.$ban_width.'px; height:'.$ban_height.'px;"';
			} else if($ban_width != '' && $ban_height == '') {
				$ban_block_size = 'style="width:'.$ban_width.'px;"';
			} else if($ban_width == '' && $ban_height != '') {
				$ban_block_size = 'style="height:'.$ban_height.'px;"';
			}
			?>
			<script>
			jQuery( document ).ready(function() {
				jQuery('#div_<?php echo $this->id ?> .ha_banner').hide();
				jQuery('#div_<?php echo $this->id ?> #1').show();
				var id_ban=1;
				var counts = <?php echo $counts ?>;
				
				setInterval(adds, <?php echo $ban_interval ?>);
				function adds(){
					if (id_ban < counts){id_ban++;} else {id_ban=1;}
					jQuery('#div_<?php echo $this->id ?> .ha_banner').hide();
					jQuery('#div_<?php echo $this->id ?> #'+id_ban).show();				
			   }
			});
			</script>
			<?php
		}
		
		$div_num =0;		
		echo '<div class="haa_banners_block_views" id="div_'.sanitize_text_field($this->id).'" '.$ban_block_size.'>';		
		foreach ( $haa_banners_view as $ids )
			{   
				$img_width = ($ids->img_width!='' && is_numeric($ids->img_width) ? (int)$ids->img_width : '');
				$img_height = ($ids->img_height!='' && is_numeric($ids->img_height) ? (int)$ids->img_height : '');
				
				if($img_width !='' && $img_height !='') {
					$size = 'style="width:'.$img_width.'px; height:'.$img_height.'px;"';
				} else if($img_width !='' && $img_height ==''){
					$size = 'style="width:'.$img_width.'px;"';
				} else if($img_height !='' && $img_width ==''){
					$size = 'style="height:'.$img_height.'px;"';
				} else {
					$size = '';
				}
				
				$upload_dir = wp_upload_dir();
				$user_dirname = $upload_dir['baseurl'].'/haa_banners/';
				
				++$div_num;
				if($ids->img_format == 'application/x-shockwave-flash'){
					if($ids->img_format == 'application/x-shockwave-flash' && $ids->img_link!=''){
						echo '
						<div id="'.$div_num.'" class="ha_banner" '.$size.' data-clicks="'.(int)$ids->id.'">
							<a href="'.esc_url($ids->img_link).'" target="'.sanitize_text_field($ids->img_target).'" title="'.sanitize_text_field($ids->title).'" '.$size.'>
								<object>
								<param name="movie" value="'.esc_url($user_dirname.$ids->name_img).'">
								<param name="wmode" value="transparent" />
								<embed width="'.$img_width.'" height="'.$img_height.'" wmode=transparent allowfullscreen="true" allowscriptaccess="always" src="'.esc_url($user_dirname.$ids->name_img).'"></embed>
								</object>
							</a>
						</div>';
					} else {						
						echo '
						<div id="'.$div_num.'" class="ha_banner" '.$size.' data-clicks="'.(int)$ids->id.'">
							<object>
							<param name="movie" value="'.esc_url($user_dirname.$ids->name_img).'">
							<param name="wmode" value="transparent" />
							<embed width="'.$img_width.'" height="'.$img_height.'" wmode=transparent allowfullscreen="true" allowscriptaccess="always" src="'.esc_url($user_dirname.$ids->name_img).'"></embed>
							</object>
						</div>';
					}
				} else  {
					if($ids->img_link!=''){
						echo '
						<div id="'.$div_num.'" class="ha_banner" '.$size.' data-clicks="'.(int)$ids->id.'">
							<a href="'.esc_url($ids->img_link).'" target="'.sanitize_text_field($ids->img_target).'" title="'.sanitize_text_field($ids->title).'" '.$size.'>
								<img src="'.$user_dirname.$ids->name_img.'" '.$size.' data-clicks="1"/>
							</a>
						</div>';
					} else {
						echo '
						<div id="'.$div_num.'" class="ha_banner" '.$size.' data-clicks="'.(int)$ids->id.'">							
							<img src="'.esc_url($user_dirname.$ids->name_img).'" '.$size.' data-clicks="1"/>							
						</div>';
					}
				}
				$sql = $wpdb->update( 
				$haa_banners_prefs_table, 
					array( 
						'img_views' => $ids->img_views+1						
					), 
					array( 'id' => $ids->id ),
					array( 
						'%d'					
					), 
					array( '%d' ) 
				);			
				$wpdb->query($sql);					
			}		
				
		echo $after_widget;
	}

	 
	public function update( $new_instance, $old_instance ) {
		if(current_user_can('edit_plugins') && $new_instance['habannerswidgetkey']=='yes') {
			$instance = $old_instance;
	
			$instance['title'] = sanitize_text_field($new_instance['title']);
			if($new_instance['jquery-rotation']==''){$instance['view'] = sanitize_text_field($new_instance['view'] );} else {$instance['view'] = '';}
			if($new_instance['rand']=='yes'){$instance['rand'] ='yes';} else {$instance['rand'] ='';};
			if($new_instance['jquery-rotation']=='yes'){$instance['jquery-rotation'] ='yes';} else {$instance['jquery-rotation'] ='';};
			$instance['jquery-rotation-seconds'] = (is_numeric($new_instance['jquery-rotation-seconds']) ? (int)$new_instance['jquery-rotation-seconds'] : '');
			$instance['jquery-rotation-width'] = (is_numeric($new_instance['jquery-rotation-width']) ? (int)$new_instance['jquery-rotation-width'] : '');
			$instance['jquery-rotation-height'] = (is_numeric($new_instance['jquery-rotation-height']) ? (int)$new_instance['jquery-rotation-height'] : '');
			$instance['position'] = (is_numeric($new_instance['position']) ? (int)$new_instance['position'] : 0);
			
			$ids_arr = '';		
			foreach ( $new_instance['ha_banner_ids'] as $ids )
			{
				$ids_arr .=(is_numeric($ids) ? (int)$ids.', ' : '');			
			}		
			$instance['ha_banner_ids'] = strip_tags(substr($ids_arr, 0, -2));
	
			return $instance;
		}
	}

	
	public function form ( $instance ) {
						
		$instance = wp_parse_args( (array) $instance, $defaults );
		apply_filters('widget_title', $instance['title'] );
		
		$id_numbers_arr = explode(", ", $instance['ha_banner_ids']);		
				
		global $wpdb, $haa_banners_prefs_table;
		$haa_banners = $wpdb->get_results( "SELECT * FROM $haa_banners_prefs_table" );
		?>
        
        <div id="<?php echo $this->id; ?>" class="haa_banners_block">       
        	<p>
            	<label><strong><?php _e('Title:', 'haa_banners_lang'); ?></strong></label><br />
		        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_html($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_html($instance['title']); ?>" />
            </p>        	
        	<h2><?php _e('Select banner:', 'haa_banners_lang'); ?></h2>
			<?php
            echo '<div id="haa_banners_block_views">';
			
            foreach ( $haa_banners as $ha_banner )
            {		
            ?>    
                <p id="<?php echo $ha_banner->id; ?>" class="p_border"><input id="<?php echo esc_attr($ha_banner->id); ?>" data-name="<?php echo esc_attr($this->get_field_name( 'ha_banner_ids' )); ?>[]" type="checkbox" name="" value="<?php echo esc_attr($ha_banner->id); ?>" <?php 
					foreach ( $id_numbers_arr as $check ) {
						if($ha_banner->id == $check){ echo 'checked="checked"';}
					} 
					?> /><label id="<?php echo $ha_banner->id; ?>"><?php echo $ha_banner->title; ?></label></p>
            <?php
            }
            echo '</div>';
            ?>
          	
            <div id="<?php echo $this->id.'_add'; ?>" class="add_banners"><?php _e('Add selected', 'haa_banners_lang'); ?></div>
            <h3><?php _e('Select position / remove:', 'ha-banners'); ?></h3>
            <div id="<?php echo $this->id.'_block'; ?>" class="block_banners_div">
            <?php
				$haa_banners_view = $wpdb->get_results( "SELECT * FROM $haa_banners_prefs_table WHERE id IN (".$instance['ha_banner_ids'].") ORDER BY FIELD(id, ".$instance['ha_banner_ids'].")" );
				
				foreach ( $haa_banners_view as $ha_banner )
            	{					
					echo '<p id="'.$this->get_field_name( 'ha_banner_ids' ).'" data-block="'.$this->id.'" data-val="'.$ha_banner->id.'" class="haa_banners_num"><input type="checkbox" name="'.$this->get_field_name( 'ha_banner_ids' ).'[]" value="'.esc_attr($ha_banner->id).'" checked="checked" />'.esc_html($ha_banner->title).'<span class="del_banners_'.$ha_banner->id.'" attr="'.$ha_banner->id.'" data-label="'.esc_attr($ha_banner->title).'">x</span></p>';
				}
			?>
            </div>            
            
      		<div>            	
            	<h3><?php _e('Basic settings:', 'haa_banners_lang'); ?></h3>				
            	<p class="ha_banner_opt">
                    <label><?php _e('Banners show:', 'haa_banners_lang'); ?> </label>
                    <input name="<?php echo esc_attr($this->get_field_name( 'view' )); ?>" type="text" size="5" value="<?php echo esc_attr($instance['view']); ?>" /><br />
                </p>
                <p class="ha_banner_opt">                    
                    <label><?php _e('Random selection:', 'haa_banners_lang'); ?> </label>
                    <input name="<?php echo esc_attr($this->get_field_name( 'rand' )); ?>" type="checkbox" value="yes" <?php if($instance['rand']=='yes'){echo 'checked="checked"';} ?> />
                </p>
                <h3>
                	<label><strong><?php _e('Enable jQuery rotation:', 'haa_banners_lang'); ?></strong> </label>
                    <input name="<?php echo esc_attr($this->get_field_name( 'jquery-rotation' )); ?>" type="checkbox" value="yes" <?php if($instance['jquery-rotation']=='yes'){echo 'checked="checked"';} ?> />
                </h3>
        		<p class="ha_banner_opt opt_grey">
                    <strong><?php _e('Settings for jQuery rotation:', 'haa_banners_lang'); ?></strong>
					<span>
                    	<label><?php _e('Interval:', 'haa_banners_lang'); ?> </label>
                    	<input name="<?php echo esc_attr($this->get_field_name( 'jquery-rotation-seconds' )); ?>" type="text" size="5" value="<?php echo esc_attr($instance['jquery-rotation-seconds']); ?>" /> 
                        <em><?php _e('seconds', 'haa_banners_lang'); ?></em>
                    </span>
            		<span>
                    	<label><?php _e('Width', 'haa_banners_lang'); ?> <em>(px)</em>: </label>
                    	<input name="<?php echo esc_attr($this->get_field_name( 'jquery-rotation-width' )); ?>" type="text" size="5" value="<?php echo esc_attr($instance['jquery-rotation-width']); ?>"/>
                        <label><?php _e('Height', 'haa_banners_lang'); ?> <em>(px)</em>: </label>
                    	<input name="<?php echo esc_attr($this->get_field_name( 'jquery-rotation-height' )); ?>" type="text" size="5" value="<?php echo esc_attr($instance['jquery-rotation-height']); ?>" />
                    </span>
    			</p>
                <input name="<?php echo esc_html($this->get_field_name( 'habannerswidgetkey' )); ?>" type="hidden" value="yes" />
            </div>            
        </div>
        <?php
        	echo '<input class="your_unique_class" style="display: none;" type="text" value="' . $this->id . '" />';
        ?>
         <script>
						
			jQuery( document ).ready(function() {
				var widget_ids = jQuery(".your_unique_class");
 				var is =0;
				var id_block=0;;
				for (var x = 0; x < widget_ids.length; x++) {
				 if(jQuery(widget_ids[x]).val() != 'haa_banners_widgets-__i__'){
					var ids = jQuery(widget_ids[x]).val();
					
					if(jQuery('.haa_banners_block').attr('id') == ''){
						if(is==0){
							jQuery('.haa_banners_block').attr('id', ids);
							jQuery('.block_banners_div').attr('id', ids+'_block');
							jQuery('.add_banners').attr('id', ids+'_add');
							return id_block = 	ids;
						}						
					}					
					++is;
				  }					  
				}
				num_i=0;	
				
				jQuery(".add_banners").off('click');
				jQuery(".add_banners").on('click', function() {			
									
					var name_block = jQuery(this).attr('id');
					var name_id = name_block.substring(0, name_block.length - 4);
					var name_div = name_id+'_block';					
										
					var arrList = jQuery('#'+name_id+' #haa_banners_block_views input:checkbox:checked').map(function(){							
						return jQuery(this).val();							
					}).get();
										
					jQuery.each( arrList, function( key, value ) {						
						var name_banners = jQuery('#'+name_id+' label#'+value).html();
						var name_input = jQuery('#'+name_id+' input#'+value).attr('data-name');
						var remBlock = jQuery('#'+name_id+' input#'+value).parent('p');
						
						addBlock(name_div, name_input, name_id, value, name_banners, remBlock);						
						
					});
					
					jQuery(".haa_banners_num span").off('click');
					jQuery( ".haa_banners_num span" ).on('click', function() {
						jQuery(this).parent('p').remove();						
					});
					
				});
				
				jQuery(".haa_banners_num span").off('click');
				jQuery( ".haa_banners_num span" ).on('click', function() {
					jQuery(this).parent('p').remove();				
				});
								
				function addBlock(name_div, name_input, name_id, value, name_banners, remBlock){
					var arrListD = jQuery('#'+name_div+' input').map(function(){							
						return jQuery(this).val();							
					}).get();
					var dop = true;
					jQuery.each( arrListD, function( key, val ) {
						if(value==val){ dop = false;}						
					});
					
					num_i++;
					if(num_i==1){
						if(dop == true){
					jQuery('#'+name_div).append('<p id="'+name_input+'" data-block="'+name_id+'" class="haa_banners_num"><input type="checkbox" name="'+name_input+'" value="'+value+'" checked="checked" />'+name_banners+'<span class="del_banners_'+value+'" attr="'+value+'" data-label="'+name_banners+'">x</span>'+'</p>');
					//remBlock.remove();
						}
					}
					num_i=0;
				}				
				
				jQuery( ".block_banners_div, #haa_banners_block_views" ).sortable({
				  revert: true
				});
				 
			});
        </script>		        
	<?php
	}
}
?>