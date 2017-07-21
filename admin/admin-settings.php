<?php
function haa_banners_options_page() {	
    global $wpdb, $haa_banners_prefs_table; 	
	$sub_count=0;
	
	if(isset($_POST['SubmitEdit']) || isset($_POST['Submit']) || isset($_POST['submit_img'])){
		$id_banner = (is_numeric($_POST['id_banner']) ? (int)$_POST['id_banner'] : 0);
		$title = sanitize_text_field($_POST['titleimg']);
		$name_img = sanitize_file_name($_POST['name_img']);
		$img_format = sanitize_text_field($_POST['img_format']);
		$img_link = sanitize_text_field($_POST['img_link']);
		$img_target = sanitize_text_field($_POST['img_target']);
		$img_width = (is_numeric($_POST['img_width']) ? (int)$_POST['img_width'] : '');
		$img_height = (is_numeric($_POST['img_height']) ? (int)$_POST['img_height'] : '');
	}
	
	
	if(isset($_POST['delete']) && isset($_POST['habannerskey']) && $_POST['habannerskey']=='yes'){
		$id_bannerEdit = (is_numeric($_POST['id_bannerEdit']) ? (int)$_POST['id_bannerEdit'] : 0);		
				
		$upload_dir = wp_upload_dir();
		$user_dirname = $upload_dir['basedir'].'/haa_banners/';		
		
		$action_del = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $haa_banners_prefs_table WHERE id = %d", $id_bannerEdit) );
		$sql = $wpdb->delete( $haa_banners_prefs_table, array( 'id' => $id_bannerEdit ) );
		$wpdb->query($sql);
				
		$file = $user_dirname.$action_del->name_img;
 
		unlink( $file );
		
		echo '<div class="notice is-dismissible">
				<p>'.__("Banner deleted successfully!", "haa_banners_lang").'</p>
				<button class="notice-dismiss" type="button">
					<span class="screen-reader-text">'.__("Hide this notification", "haa_banners_lang").'.</span>
				</button>
			</div>';
	}
	
	if(isset($_POST['edit']) && isset($_POST['habannerskey']) && $_POST['habannerskey']=='yes'){
		$id_bannerEdit = (is_numeric($_POST['id_bannerEdit']) ? (int)$_POST['id_bannerEdit'] : 0);		
		$action_de = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $haa_banners_prefs_table WHERE id = %d", $id_bannerEdit)  );
    	
		$title = sanitize_text_field($action_de->title);
		$name_img = sanitize_text_field($action_de->name_img);
		$img_format = sanitize_text_field($action_de->img_format);
		$img_link = sanitize_text_field($action_de->img_link);
		$img_target = sanitize_text_field($action_de->img_target);
		$img_width = (is_numeric($action_de->img_width) ? (int)$action_de->img_width : '');
		$img_height = (is_numeric($action_de->img_height) ? (int)$action_de->img_height : '');
	}
	
	if(isset($_POST['SubmitEdit']) && isset($_POST['habannerskey']) && $_POST['habannerskey']=='yes' && $name_img!='' && $title!='') {				
		$sql = $wpdb->update( 
				$haa_banners_prefs_table, 
				array( 
					'title' => $title,
					'name_img' => $name_img,
					'img_format' => $img_format,
					'img_link' => $img_link,
					'img_target' => $img_target,
					'img_width' => $img_width,
					'img_height' => $img_height	
				), 
				array( 'id' => $id_banner ), 
				array( 
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d'
				), 
				array( '%d' ) 
			);			
        $wpdb->query($sql);
		
		echo '<div class="notice is-dismissible">
				<p>'.__("Banner successfully saved", "haa_banners_lang").'!</p>
				<button class="notice-dismiss" type="button">
					<span class="screen-reader-text">'.__("Hide this notification", "haa_banners_lang").'.</span>
				</button>
			</div>';
			
		$sub_count=1;
	}
	
	if(isset($_POST['Submit']) && isset($_POST['habannerskey']) && $_POST['habannerskey']=='yes' && $name_img!='' && $title!=''){
		$sql = $wpdb->prepare( 
			"
				INSERT INTO $haa_banners_prefs_table
				( title, name_img, img_format, img_link, img_target, img_width, img_height, img_clicks, img_views )
				VALUES ( %s, %s, %s, %s, %s, %d, %d, %d, %d )
			", 
			$title, 
			$name_img, 
			$img_format, 
			$img_link, 
			$img_target, 
			$img_width, 
			$img_height,
			0,
			0
		) ;		
									
		
		$wpdb->query($sql);	
		
		echo '<div class="notice is-dismissible">
				<p>'.__("Banner successfully saved", "haa_banners_lang").'!</p>
				<button class="notice-dismiss" type="button">
					<span class="screen-reader-text">'.__("Hide this notification", "haa_banners_lang").'.</span>
				</button>
			</div>';
		
		$sub_count=1;
	}
?>
<div class="haa_banner-block">
    <h1>HA Banners</h1>        
    <div id="sub_count" style="display:none;"><?php echo $sub_count; ?></div>
    <?php
	 if(current_user_can('upload_files') && current_user_can('edit_plugins')) {
	?>
    <form id="haa_banners-form" action="<? echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
    	
        <p>
            <?php
			
			$upload_dir_img = wp_upload_dir();
			$user_dirname_img = $upload_dir_img['baseurl'].'/haa_banners/';			
			$uploaddir = $upload_dir_img['basedir'].'/haa_banners/';			
						
            if(isset($_POST['submit_img']) && isset($_FILES['uploadfile']['name']) && $_POST['submit_img']!='' && $_FILES['uploadfile']['name']!='' && isset($_POST['habannerskey']) && $_POST['habannerskey']=='yes'){                			
				               
                $file_name_red = mb_strtolower($_FILES['uploadfile']['name']);
                $file_name = str_replace(" ","",$file_name_red);
                $uploadfile = $uploaddir.basename($file_name);
                                                        
                if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile))
                    {
                    echo "";
                    }
                    else { 
                        echo '<div class="notice is-dismissible notice-warning">
                            <p>'.__("Could not load file", "haa_banners").'!</p>
                            <button class="notice-dismiss" type="button">
                                <span class="screen-reader-text">'.__("Hide this notification", "haa_banners_lang").'.</span>
                            </button>
                        </div>';
                        exit; 
                    }						
                
                $name_img = $file_name;
                $img_format = sanitize_text_field($_FILES['uploadfile']['type']);
                $img_width = (is_numeric($_POST['img_width']) ? (int)$_POST['img_width'] : '');
                $img_height = (is_numeric($_POST['img_height']) ? (int)$_POST['img_height'] : '');
            }
			$user_link_img = esc_url($user_dirname_img.$name_img);
            if(isset($name_img) && $name_img!='' && $img_format=='application/x-shockwave-flash'){
                if(isset($img_width) && $img_width=='' || $img_width==0 ) $img_width=100;
                if(isset($img_height) && $img_height=='' || $img_height==0) $img_height=100;
								         
                echo '<object id="banner_img" type="application/x-shockwave-flash" data="'.$user_link_img.'" width="'.$img_width.'" height="'.$img_height.'"></object>';
            } else if(isset($name_img) && $name_img!='') {
				if(isset($img_width) && $img_width==0) $img_width='';
                if(isset($img_height) && $img_height==0) $img_height='';					
                echo '<img id="banner_img" src="'.$user_link_img.'" width="'.$img_width.'" height="'.$img_height.'" />';
            }
            ?>
        </p>
            
        <div class="haa_banners-left">
            <p>
                <label><?php _e('Select a file*', 'haa_banners_lang'); ?> <span>(jpg, png, gif, swf):</span></label>
                <input name="uploadfile" type="file"><br /><br />
                <input class="button" type="submit" name="submit_img" value="<?php _e('download banner', 'haa_banners_lang'); ?>" />
            </p>
            <p style="display:none" class="uploadfile"><?php _e('*Download file', 'haa_banners_lang'); ?></p>
            <p class="p_left">
                <label><?php _e('Width', 'haa_banners_lang'); ?> <span>(px)</span>: </label>
                <input name="img_width" type="text" value="<?php if(isset($img_width) && $img_width!=0) echo (int)$img_width; ?>" size="5" placeholder="100" />
            </p>
            <p class="p_right">
                <label><?php _e('Height', 'haa_banners_lang'); ?> <span>(px)</span>: </label>
                <input name="img_height" type="text" value="<?php if(isset($img_height) && $img_width!=0) echo (int)$img_height; ?>" size="5" placeholder="100" />                
            
                <input name="name_img" id="name_img" type="hidden" value="<?php if(isset($name_img)) echo esc_html($name_img); ?>" />
                <input name="img_format" type="hidden" value="<?php if(isset($img_format)) echo esc_html($img_format); ?>" />
                <input name="id_banner" type="hidden" value="<?php if(isset($id_banner)) {echo esc_html($id_banner);} else if(isset($id_bannerEdit)) {echo esc_html($id_bannerEdit);} ?>" />
            </p>           
        </div>
        
        <div class="haa_banners-left">
            <p>
                <label><?php _e('Title banner', 'haa_banners_lang'); ?>*:</label>
                <input name="titleimg" id="titleimg" type="text" size="30" value="<?php if(isset($title)) echo esc_html($title); ?>" />                    
            </p>
            <p style="display:none" class="titleimg">*<?php _e('Add name', 'haa_banners_lang'); ?></p>
            <p>
                <label><?php _e('Link:', 'haa_banners_lang'); ?></label>
                <input name="img_link" type="text" size="30" value="<?php if(isset($img_link)) echo esc_url($img_link); ?>" placeholder="http://client-site.com" />
            </p>
            <p>           	
                <label><?php _e('Format link', 'haa_banners_lang'); ?> <span>(target)</span>: </label>
                <select id="img_target" style="width: 80px" name="img_target">
                    <option <?php if($img_target=='_self') echo 'selected="selected"'; ?> value="<?php echo esc_html('_self'); ?>">_self</option>
                    <option <?php if($img_target=='_top') echo 'selected="selected"'; ?> value="<?php echo esc_html('_top'); ?>">_top</option>
                    <option <?php if($img_target=='_blank') echo 'selected="selected"'; ?> value="<?php echo esc_html('_blank'); ?>">_blank</option>
                    <option <?php if($img_target=='_parent') echo 'selected="selected"'; ?> value="<?php echo esc_html('_parent'); ?>">_parent</option>
                </select>
                <input name="habannerskey" type="hidden" value="yes" />
            </p>
            <?php
	 		}
			?>
        </div>
        <div class="clear"></div>
        <p>
            <?php
            if(isset($_POST['edit'])){				
                echo '<input id="SubmitEdit" class="button-primary" type="submit" name="SubmitEdit" value="'.__("Save", "haa_banners_lang").'" />';
            } else {				
                echo '<input id="Submit" class="button-primary" type="submit" name="Submit" value="'.__("Save", "haa_banners_lang").'"  />';
            }
            ?>
            <input id="reset_banner" class="button" type="submit" value="<?php _e('Cancel', 'haa_banners_lang'); ?>" />
        </p>
    </form>
</div>


<?php
    global $wpdb, $haa_banners_prefs_table;				
    $newtable = $wpdb->get_results("SELECT * FROM $haa_banners_prefs_table" );
?>
    <table class="widefat">
        <thead>
            <tr>
                <th>Id</th>
                <th><?php _e('Name', 'haa_banners_lang'); ?></th>       
                <th><?php _e('Link', 'haa_banners_lang'); ?></th>                
                <th>Image / SWF</th>
                <th><?php _e('View', 'haa_banners_lang'); ?></th>       
                <th><?php _e('Click', 'haa_banners_lang'); ?></th>
                <th><?php _e('Edit / Delete', 'haa_banners_lang'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Id</th>
                <th><?php _e('Name', 'haa_banners_lang'); ?></th>       
                <th><?php _e('Link', 'haa_banners_lang'); ?></th>                
                <th>Image / SWF</th>
                <th><?php _e('View', 'haa_banners_lang'); ?></th>       
                <th><?php _e('Click', 'haa_banners_lang'); ?></th>
                <th><?php _e('Edit / Delete', 'haa_banners_lang'); ?></th>
            </tr>
        </tfoot>
        <tbody>           
			<?php
                foreach ( $newtable as $page )
                {
                    echo '<tr>';
                    echo '<td>'.$page->id.'</td>';
                    echo '<td>'.$page->title.'</td>';
                    echo '<td>'.$page->img_link.'</td>';
					echo '<td>'.$page->name_img.'</td>';
                    echo '<td>'.$page->img_views.'</td>';
                    echo '<td>'.$page->img_clicks.'</td>';                    
                    echo '<td>';
                ?>                
                    <form name="action_banner" action="<? echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
                        <input name="edit" type="submit" class="button" value="<?php _e('Edit', 'haa_banners_lang'); ?>" />
                        <input name="delete" type="submit" class="button" value="<?php _e('Delete', 'haa_banners_lang'); ?>" />
                        <input name="id_bannerEdit" type="hidden" value="<?php echo $page->id; ?>" />
                        <input name="habannerskey" type="hidden" value="yes" />
                    </form>
                <?php
                    echo '</td>';
                    echo '</tr>';
                }
            ?>            
        </tbody>
    </table>
<?php
}

?>