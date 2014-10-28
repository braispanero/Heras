<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
 
class Essential_Grids_Widget extends WP_Widget {
	
    public function __construct(){
    	
        // widget actual processes
     	$widget_ops = array('classname' => 'widget_ess_grid', 'description' => __('Displays certain Essential Grid on the page') );
        parent::__construct('ess-grid-widget', __('Essential Grid', EG_TEXTDOMAIN), $widget_ops);
    }
 
 
    /**
     * the form
     */
    public function form($instance) {
		
    	$arrGrids = Essential_Grid::get_grids_short();
		
		if(empty($arrGrids)){
			echo __("No Essential Grids found, Please create at least one!", EG_TEXTDOMAIN);
		}else{
			
			$field = "ess_grid";
			$fieldPages = "ess_grid_pages";
			$fieldCheck = "ess_grid_homepage";
			$fieldTitle = "ess_grid_title";
			
	    	$gridID = @$instance[$field];
	    	$homepage = @$instance[$fieldCheck];
	    	$pagesValue = @$instance[$fieldPages];
	    	$title = @$instance[$fieldTitle];
	    	
			$fieldID = $this->get_field_id( $field );
			$fieldName = $this->get_field_name( $field );
			
			$fieldID_check = $this->get_field_id( $fieldCheck );
			$fieldName_check = $this->get_field_name( $fieldCheck );
			$checked = "";
			if($homepage == "on")
				$checked = "checked='checked'";

			$fieldPages_ID = $this->get_field_id( $fieldPages );
			$fieldPages_Name = $this->get_field_name( $fieldPages );
			
			$fieldTitle_ID = $this->get_field_id( $fieldTitle );
			$fieldTitle_Name = $this->get_field_name( $fieldTitle );
			
		?>
			<label for="<?php echo $fieldTitle_ID; ?>"><?php _e('Title', EG_TEXTDOMAIN); ?>:</label>
			<input type="text" name="<?php echo $fieldTitle_Name; ?>" id="<?php echo $fieldTitle_ID; ?>" value="<?php echo $title; ?>" class="widefat">
			
			<br><br>
			
			<?php _e('Choose Essential Grid', EG_TEXTDOMAIN); ?>:
			<select name="<?php echo $fieldName; ?>" id="<?php echo $fieldID; ?>">
				<?php
				foreach($arrGrids as $id => $name){
					?>
					<option value="<?php echo $id; ?>"<?php echo ($gridID == $id) ? ' selected="selected"' : ''; ?>><?php echo $name; ?></option>
					<?php
				}
				?>
			</select>
			
			<div style="padding-top:10px;"></div>
			
			<label for="<?php echo $fieldID_check; ?>"><?php _e('Home Page Only', EG_TEXTDOMAIN); ?>:</label>
			<input type="checkbox" name="<?php echo $fieldName_check; ?>" id="<?php echo $fieldID_check; ?>" <?php echo $checked; ?> >
			<br><br>
			<label for="<?php echo $fieldPages_ID; ?>"><?php _e('Pages: (example: 3,8,15)', EG_TEXTDOMAIN); ?></label>
			<input type="text" name="<?php echo $fieldPages_Name; ?>" id="<?php echo $fieldPages_ID; ?>" value="<?php echo $pagesValue; ?>">
			
			<div style="padding-top:10px;"></div>
		<?php
		}	//else
		 
    }
	
 
    /**
     * update
     */
    public function update($new_instance, $old_instance) {
    	
        return($new_instance);
    }

    
    /**
     * widget output
     */
    public function widget($args, $instance) {
    	
		$grid_id = $instance["ess_grid"];
		$title = @$instance["ess_grid_title"];
		
		$homepageCheck = @$instance["ess_grid_homepage"];
		$homepage = "";
		if($homepageCheck == "on")
			$homepage = "homepage";
		
		$pages = $instance["ess_grid_pages"];
		if(!empty($pages)){
			if(!empty($homepage))
				$homepage .= ",";
			$homepage .= $pages;
		}
				
		if(empty($grid_id))
			return(false);
			
		//widget output
		$beforeWidget = $args["before_widget"];
		$afterWidget = $args["after_widget"];
		$beforeTitle = $args["before_title"];
		$afterTitle = $args["after_title"];
		
		echo $beforeWidget;
		
		if(!empty($title))
			echo $beforeTitle.$title.$afterTitle;
		
		Essential_Grid::output_essential_grid($grid_id,$homepage);
		
		echo $afterWidget;						
    }
 
}


?>