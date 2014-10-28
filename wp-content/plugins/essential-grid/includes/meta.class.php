<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
 
class Essential_Grid_Meta {
	
	
	/**
	 * Add a new Meta 
	 */
	public function add_new_meta($new_meta){
		
		if(!isset($new_meta['handle']) || strlen($new_meta['handle']) < 3) return __('Wrong Handle received', EG_TEXTDOMAIN);
		if(!isset($new_meta['name']) || strlen($new_meta['name']) < 3) return __('Wrong Name received', EG_TEXTDOMAIN);
		
		$metas = $this->get_all_meta();
		
		foreach($metas as $meta){
			if($meta['handle'] == $new_meta['handle']) return __('Meta with handle already exist, choose a different handle', EG_TEXTDOMAIN);
		}
		
		$new = array('handle' => $new_meta['handle'], 'name' => $new_meta['name'], 'type' => $new_meta['type'], 'default' => @$new_meta['default']);
		
		if($new_meta['type'] == 'select'){
			if(!isset($new_meta['sel']) || strlen($new_meta['sel']) < 3) return __('Wrong Select received', EG_TEXTDOMAIN);
			
			$new['select'] = $new_meta['sel'];
		}
			
		
		$metas[] = $new;
		
		$do = update_option('esg-custom-meta', $metas);
		
		return true;
	}
	
	
	/**
	 * change meta by handle
	 */
	public function edit_meta_by_handle($edit_meta){
		
		if(!isset($edit_meta['handle']) || strlen($edit_meta['handle']) < 3) return __('Wrong Handle received', EG_TEXTDOMAIN);
		if(!isset($edit_meta['name']) || strlen($edit_meta['name']) < 3) return __('Wrong Name received', EG_TEXTDOMAIN);
		
		$metas = $this->get_all_meta();
		
		foreach($metas as $key => $meta){
			if($meta['handle'] == $edit_meta['handle']){
				$metas[$key]['select'] = @$edit_meta['sel'];
				$metas[$key]['name'] = $edit_meta['name'];
				$metas[$key]['default'] = @$edit_meta['default'];
				$do = update_option('esg-custom-meta', $metas);
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Remove Meta 
	 */
	public function remove_meta_by_handle($handle){
		
		$metas = $this->get_all_meta();
		
		foreach($metas as $key => $meta){
			if($meta['handle'] == $handle){
				unset($metas[$key]);
				$do = update_option('esg-custom-meta', $metas);
				return true;
			}
		}
		
		return __('Meta not found! Wrong handle given.', EG_TEXTDOMAIN);
	}
	
	
	/**
	 * get all custom metas
	 */
	public function get_all_meta(){
	
		$meta = get_option('esg-custom-meta', array());
		
		return $meta;
	}
	
	
	/**
	 * get all handle of custom metas 
	 */
	public function get_all_meta_handle(){
		$metas = array();
		
		$meta = get_option('esg-custom-meta', array());
		
		if(!empty($meta)){
			foreach($meta as $m){
				$metas[] = 'eg-'.$m['handle'];
			}
		}
		
		if(Essential_Grid_Woocommerce::is_woo_exists()){
			$meta = Essential_Grid_Woocommerce::get_meta_array();
			
			if(!empty($meta)){
				foreach($meta as $handle => $name){
					$metas[] = $handle;
				}
			}
			
		}
		
		return $metas;
	}
	
	
	/**
	 * insert comma seperated string, it will return an array of it
	 */
	public function prepare_select_by_string($string){
		
		return explode(',', $string);
		
	}
	
	
	/**
	 * check if post has meta
	 */
	public function get_meta_value_by_handle($post_id, $handle){
		$metas = get_post_meta($post_id,$handle,true);
		if(is_array($metas))
			$text = @$metas[$handle];
		else
			$text = $metas;
		
		//check if custom meta from us and if it is an image. If yes, output URL instead of ID
		$cmeta = $this->get_all_meta();
		
		if(!empty($cmeta)){
			foreach($cmeta as $me){
				if('eg-'.$me['handle'] == $handle){
					if($me['type'] == 'image'){
						if(intval($text) > 0){
							//get URL to Image
							$img = wp_get_attachment_image_src($text, 'full');
							if($img !== false){
								$text = $img[0];
							}else{
								$text = '';
							}
						}else{
							$text = '';
						}
					}
					if($text == '' && isset($me['default'])){
						$text = $me['default'];
					}
					break;
				}
			}
		}
		
		//check woocommerce
		if(Essential_Grid_Woocommerce::is_woo_exists()){
			$wc_text = Essential_Grid_Woocommerce::get_value_by_meta($post_id, $handle);
			if($wc_text !== '') $text = $wc_text;
		}
		
		
		return $text;
	}
	
	
	/**
	 * replace all metas with corresponding text
	 */
	public function replace_all_meta_in_text($post_id, $text){
		$cmeta = $this->get_all_meta();
		
		//process meta tags:
		$arr_matches = array();

		preg_match_all("/%[^%]*%/", $text, $arr_matches);
		
		if(!empty($arr_matches)){
			foreach($arr_matches as $matches){
				if(is_array($matches)){
					foreach($matches as $match){
						$meta = trim(str_replace('%', '', $match));
						$meta_value = get_post_meta($post_id, $meta, true);
						if(!empty($cmeta)){
							foreach($cmeta as $me){
								if('eg-'.$me['handle'] == $meta){
									if($me['type'] == 'image'){
										if(intval($meta_value) > 0){
											//get URL to Image
											$img = wp_get_attachment_image_src($meta_value, 'full');
											if($img !== false){
												$meta_value = $img[0];
											}else{
												$meta_value = '';
											}
										}else{
											$meta_value = '';
										}
									}
									if($meta_value == '' && isset($me['default'])){
										$meta_value = $me['default'];
									}
									break;
								}
							}
						}
						
						//check woocommerce
						if(Essential_Grid_Woocommerce::is_woo_exists()){
							$wc_text = Essential_Grid_Woocommerce::get_value_by_meta($post_id, $meta);
							if($wc_text !== '') $meta_value = $wc_text;
						}
						
						$text = str_replace($match,$meta_value,$text);
					}
				}
			}
		}
		
		return $text;
	}
	
	
	/**
	 * replace all metas with corresponding text
	 */
	public function replace_all_custom_element_meta_in_text($values, $text){
		$cmeta = $this->get_all_meta();
		
		//process meta tags:
		$arr_matches = array();

		preg_match_all("/%[^%]*%/", $text, $arr_matches);
		
		if(!empty($arr_matches)){
			foreach($arr_matches as $matches){
				if(is_array($matches)){
					foreach($matches as $match){
						$meta = str_replace('%', '', $match);
						$meta_value = @$values[$meta];
						
						if(!empty($cmeta)){
							foreach($cmeta as $me){
								if('eg-'.$me['handle'] == $meta){
									if($me['type'] == 'image'){
										if(intval($meta_value) > 0){
											//get URL to Image
											$img = wp_get_attachment_image_src($meta_value, 'full');
											if($img !== false){
												$meta_value = $img[0];
											}else{
												$meta_value = '';
											}
										}else{
											$meta_value = '';
										}
									}
									break;
								}
							}
						}
						
						
						$text = str_replace($match,$meta_value,$text);
					}
				}
			}
		}
		
		return $text;
	}
	
	
	/**
	 * get video ratios from post
	 */
	public function get_post_video_ratios($post_id){
	
		$ratio['vimeo'] = get_post_meta($post_id, 'eg_vimeo_ratio', true);
		$ratio['youtube'] = get_post_meta($post_id, 'eg_youtube_ratio', true);
		$ratio['html5'] = get_post_meta($post_id, 'eg_html5_ratio', true);
		$ratio['soundcloud'] = get_post_meta($post_id, 'eg_soundcloud_ratio', true);
		
		return $ratio;
	}
	
	
	/**
	 * get video ratios from custom element
	 */
	public function get_custom_video_ratios($values){
		if(!isset($values['custom-ratio'])) $values['custom-ratio'] = '0';
		
		$ratio['vimeo'] = $values['custom-ratio'];
		$ratio['youtube'] = $values['custom-ratio'];
		$ratio['html5'] = $values['custom-ratio'];
		$ratio['soundcloud'] = $values['custom-ratio'];
		
		return $ratio;
	}
	
}

?>