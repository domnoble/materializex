<?php
/**
 * Author : Dom Noble
 */
namespace App;

add_action( 'admin_enqueue_scripts', function ( $hook ) {
  if (isset($_GET['page'])){
    if( is_admin() && $_GET['page'] === "mx-settings" || is_admin()  && $_GET['page'] === "mx-color-settings" || is_admin()  && $_GET['page'] === "mx-images-settings" || is_admin()  && $_GET['page'] === "mx-style-settings" || is_admin()  && $_GET['page'] === "mx-layout-settings"  ) {
        // Add the color picker css file
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script('jquery-masonry');
        wp_enqueue_style( 'matx-admin-styles',asset_path('styles/admin.css'), false, null);
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'matx-admin-scripts', asset_path('scripts/admin.js'), array( 'wp-color-picker' ), false, true );
    }
  }
} );

class MatXOptions{

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $shortname;
    public $layout_options;
    public $image_options;
    public $color_options;
    public $current_layout;

    /**
     * Start up
     */
    public function __construct(){
      $this->shortname = 'mx';
      $this->layout_options = array(
        array("name" => "Layout Options",
              "type" => "sub-section-3",
              "category" => "mx-layout-options",
              "desc" => "This section you can modify the layout options for MaterializeX",
              "layout" => "list"
        ),
        array("name" => "Sidebar Position",
              "id" => $this->shortname."_sidebar_alignment",
              "type" => "radio",
              "desc" => "Which side would you like your sidebar?",
              "options" => array("left" => "Left", "right" => "Right"),
              "parent" => "mx-layout-options",
              "std" => "right"
        ),
        array("name" => "Pages to show in Navigation Bar",
              "desc" => "Select the pages you want to include. All pages are excluded by default",
              "id" => $this->shortname."_nav_pages",
              "type" => "multi-select",
              "options" => $this->get_formatted_page_array($this->shortname."_nav_pages"),
              "parent" => "mx-layout-options",
              "std" => "none"
        ),
        array("name" => "category posts to show on the front page",
              "desc" => "Select the category you want to include. All pages are excluded by default",
              "id" => $this->shortname."_front_page_first_section",
              "type" => "select",
              "options" => $this->get_category_array($this->shortname."_nav_pages"),
              "parent" => "mx-layout-options",
              "std" => $this->get_category_array($this->shortname."_nav_pages")
        )
      );
      $this->image_options = array(
        array("name" => "Images Options",
              "type" => "sub-section-3",
              "category" => "mx-image-options",
              "desc" => "Here you can change image source that will be used in the various theme options",
              "layout" => "grid"
        ),
        array("name" => "Logo Image",
              "desc" => "Set the image to use for the Logo. ",
              "id" => $this->shortname."_logo_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        ),
        array("name" => "Favicon",
              "desc" => "Set the image to use for the Favicon. ",
              "id" => $this->shortname."_favicon_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        ),
        array("name" => "Apple Touch",
              "desc" => "Set the image to use for the Apple touch icon. ",
              "id" => $this->shortname."_apple_touch_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        ),
        array("name" => "Header Image",
              "desc" => "Set the image to use for the header background. ",
              "id" => $this->shortname."_header_background_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        ),
        array("name" => "Background Image",
              "desc" => "Set the image to use for the main background. ",
              "id" => $this->shortname."_main_background_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        ),
        array("name" => "Footer Image",
              "desc" => "Set the image to use for the Footer background. ",
              "id" => $this->shortname."_footer_background_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        ),
        array("name" => "Card Image",
              "desc" => "Set the image to use for the cards background. ",
              "id" => $this->shortname."_card_background_image",
              "type" => "text",
              "parent" => "mx-image-options",
              "std" => ""
        )
      );

      $this->color_options = array(
        array("name" => "Css Color Options",
              "type" => "sub-section-3",
              "category" => "mx-color-options",
              "desc" => "Here you can select hex colors that will be used in the theme's SASS and in CSS overrides",
              "layout" => "grid"
        ),
        array("name" => "Primary Color",
              "desc" => "Set the primary color",
              "id" => $this->shortname."_primary_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#358ccb"
        ),
        array("name" => "Secondary Color",
              "desc" => "Set the secondary color",
              "id" => $this->shortname."_secondary_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#2196f3"
        ),
        array("name" => "Heading Color",
              "desc" => "Set the heading color",
              "id" => $this->shortname."_heading_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#ef6c00"
        ),
        array("name" => "Link Color",
              "desc" => "Set the link color",
              "id" => $this->shortname."_link_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#ff9800"
        ),
        array("name" => "Warning Color",
              "desc" => "Set the warning alert color",
              "id" => $this->shortname."_warning_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#ffc107"
        ),
        array("name" => "Info Color",
              "desc" => "Set the info alert color",
              "id" => $this->shortname."_info_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#03a9f4"
        ),
        array("name" => "Error Color",
              "desc" => "Set the error alert color",
              "id" => $this->shortname."_error_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#d50000"
        ),
        array("name" => "Success Color",
              "desc" => "Set the success alert color",
              "id" => $this->shortname."_success_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#64dd17"
        ),
        array("name" => "Slider Indicator Color",
              "desc" => "Set the slider indicators color",
              "id" => $this->shortname."_slider_indicator_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#358ccb"
        ),
        array("name" => "Chip Bg Color",
              "desc" => "Set the chip background color",
              "id" => $this->shortname."_chip_bg_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#e0e0e0"
        ),
        array("name" => "Card Bg Color",
              "desc" => "Set the cards background color",
              "id" => $this->shortname."_card_bg_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#ffffff"
        ),
        array("name" => "Collapsible Header Color",
              "desc" => "Set the Collapsible header color",
              "id" => $this->shortname."_collapsible_header_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#ffffff"
        ),
        array("name" => "Collapsible Border Color",
              "desc" => "Set the collapsible border color",
              "id" => $this->shortname."_collapsible_border_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#dddddd"
        ),
        array("name" => "Dropdown Bg Color",
              "desc" => "Set the Dropdown Bg color",
              "id" => $this->shortname."_dropdown_bg_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#ffffff"
        ),
        array("name" => "Dropdown Hover Bg Color",
              "desc" => "Set the dropdown hover bg color",
              "id" => $this->shortname."_dropdown_hover_bg_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#eeeeee"
        ),
        array("name" => "Pattern Color",
              "desc" => "Set the pattern color",
              "id" => $this->shortname."_pattern_color",
              "type" => "color-picker",
              "parent" => "mx-color-options",
              "std" => "#3f51b5"
        )
      );
      $this->style_options = array(
        array("name" => "Style Options",
              "type" => "sub-section-3",
              "category" => "mx-style-options",
              "desc" => "This section you can modify the styles of individual components using the material design colour palette to change the color of individual components or adding css based background images and textures",
              "layout" => "grid"
        ),
        array("name" => "Header Class",
              "desc" => "Set the classes for the header background",
              "id" => $this->shortname."_header_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Header Menu Class",
              "desc" => "Set the classes for the header menu",
              "id" => $this->shortname."_header_menu_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Breadcrumbs Class",
              "desc" => "Set the classes for the breadcrumbs nav bar",
              "id" => $this->shortname."_breadcrumbs_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Search Bar Class",
              "desc" => "Set the classes for the search bar",
              "id" => $this->shortname."_search_bar_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Main Background Class",
              "desc" => "Set the classes for the main background",
              "id" => $this->shortname."_main_background_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Cards Class",
              "desc" => "Set the extra classes for the cards",
              "id" => $this->shortname."_card_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Footer Class",
              "desc" => "Set the classes for the footer",
              "id" => $this->shortname."_footer_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        ),
        array("name" => "Footer Copyright Class",
              "desc" => "Set the classes for the footer copyright background",
              "id" => $this->shortname."_footer_copyright_class",
              "type" => "text",
              "parent" => "mx-style-options",
              "std" => ""
        )
      );

      add_action( 'admin_menu', array( $this, 'add_admin' ) );
    }

    /**
     * Get array of pages
     */
    private function get_formatted_page_array(){
		  global $suffusion_pages_array;
      if (isset($suffusion_pages_array) && $suffusion_pages_array != null) {
        return $suffusion_pages_array;
      }
      $ret = array();
      $pages = get_pages('sort_column=menu_order');
      if ($pages != null) {
        foreach ($pages as $page) {
          if (is_null($suffusion_pages_array)) {
            $ret[$page->ID] = array("title" => $page->post_title, "depth" => count(get_ancestors($page->ID, 'page')));
          }
        }
      }
      if ($suffusion_pages_array == null) {
        $suffusion_pages_array = $ret;
        return $ret;
      }	else {
        return $suffusion_pages_array;
      }
	 }

   /**
    * Get category array
    */
	function get_category_array(){
    global $suffusion_category_array;
		if (isset($suffusion_category_array) && $suffusion_category_array != null) {
			return $suffusion_category_array;
		}
		$ret = array();
		$args = array('orderby' => 'name','parent' => 0);
	  $categories = get_categories( $args );
	  if($categories != null){
		  foreach ($categories as $category) {
				if (is_null($suffusion_category_array)) {
					$ret[$category->cat_ID] = array("name" => $category->name, "number" => $category->count);
				}
			}
		}
		if ($suffusion_category_array == null) {
			$suffusion_category_array = $ret;
			return $ret;
		}
		else {
			return $suffusion_category_array;
		}
	}

  /**
   * Creates the opening markup.
   *
   */
  public function create_wrap_before(){
    echo '<div class="wrap">';
  }

  /**
   * Creates the closing markup.
   *
   */
  public function create_wrap_after(){
    echo '</div>';
  }
    /**
     * Creates the closing markup for each option.
     *
     * @param $value
     * @return void
     */
    private function create_opening_tag($value,$current_layout) {
      switch($current_layout){
        case "grid":
          echo "<div class='grid-item'>";
          break;
        case "list":
          echo "<li>";
          break;
        case "table":
          echo "<tr>";
          break;
      }
      if (isset($value['name'])) {
        echo "<h3>" . $value['name'] . "</h3>";
      }
    }

    /**
     * Creates the closing markup for each option.
     *
     * @param $value
     * @return void
     */
    private function create_closing_tag($value,$current_layout) {
      if (isset($value['desc']) && !(isset($value['type']) && $value['type'] == 'checkbox')) {
        echo "<p>" . $value['desc'] . "</p><br />";
      }
      if (isset($value['note'])) {
        echo "<span class=\"note\">".$value['note']."</span>";
      }
      switch($current_layout){
        case "grid":
          echo "</div>";
          break;
        case "list":
          echo "</li>";
          break;
        case "table":
          echo "</tr>";
          break;
      }
    }

    /**
     * Creates the text section.
     *
     * @param $value
     * @return void
     */
    private function create_section_for_text($value,$current_layout) {
      if(isset($value['id'])){
        $this->create_opening_tag($value,$current_layout);
        $text = "";
        if (get_option($value['id']) === FALSE) {
          $text = $value['std'];
        } else {
        $text = get_option($value['id']);
        }
        echo '<input type="text" id="'.$value['id'].'" placeholder="enter your title" name="'.$value['id'].'" value="'.$text.'" />'."\n";
        $this->create_closing_tag($value,$current_layout);
      }
	 }

   /**
    * Creates the textarea section.
    *
    * @param $value
    * @return void
    */
	private function create_section_for_textarea($value,$current_layout) {
		$this->create_opening_tag($value,$current_layout);
		echo '<textarea name="'.$value['id'].'" type="textarea" cols="100" rows="3">'."\n";
		if ( get_option( $value['id'] ) != "") {
			echo get_option( $value['id'] );
		} else {
			echo $value['std'];
		}
		echo '</textarea>';
		$this->create_closing_tag($value,$current_layout);
	}

  /**
   * Creates the colour picker section.
   *
   * @param $value
   * @return void
   */
	private function create_section_for_color_picker($value,$current_layout) {
		$this->create_opening_tag($value,$current_layout);
		$color_value = "";
		if (get_option($value['id']) === FALSE) {
			$color_value = $value['std'];
		} else {
			$color_value = get_option($value['id']);
		}
		echo '<div class="color-picker">'."\n";
		echo '<input type="text" id="'.$value['id'].'" name="'.$value['id'].'" value="'.$color_value.'" class="color" />';
		echo '<br/> Click to select color<br/>'."\n";
		echo "</div>\n";
		$this->create_closing_tag($value,$current_layout);
	}

  /**
   * Creates the radio section.
   *
   * @param $value
   * @return void
   */
	private function create_section_for_radio($value,$current_layout) {
		$this->create_opening_tag($value,$current_layout);
		foreach ($value['options'] as $option_value => $option_text) {
			$checked = ' ';
			if (get_option($value['id']) == $option_value) {
				$checked = ' checked="checked" ';
			}
			else if (get_option($value['id']) === FALSE && $value['std'] == $option_value){
				$checked = ' checked="checked" ';
			}
			else {
				$checked = ' ';
			}
			echo '<div class="mnt-radio"><input type="radio" name="'.$value['id'].'" value="'.
				$option_value.'" '.$checked."/>".$option_text."</div>\n";
		}
		$this->create_closing_tag($value,$current_layout);
	 }

   /**
    * Creates the multi-select section.
    *
    * @param $value
    * @return void
    */
   private function create_section_for_multi_select($value,$current_layout) {
		$this->create_opening_tag($value,$current_layout);
		echo '<ul class="mnt-checklist" id="'.$value['id'].'" >'."\n";
		foreach ($value['options'] as $option_value => $option_list) {
			$checked = " ";
			if (get_option($value['id']."_".$option_value)) {
				$checked = " checked='checked' ";
			}
			echo "<li>\n";
			echo '<input type="checkbox" name="'.$value['id']."_".$option_value.'" value="true" '.$checked.' class="depth-'.($option_list['depth']+1).'" />'.$option_list['title']."\n";
			echo "</li>\n";
		}
		echo "</ul>\n";
		$this->create_closing_tag($value,$current_layout);
	}

  /**
   * Creates the category select section.
   * @param $page_section
   * @param $value
   * @return void
   */
  private function create_section_for_category_select($page_section,$value,$current_layout) {
		$this->create_opening_tag($value,$current_layout);
		$all_categoris='';
    echo '<div class="wrap" id="'.$value['id'].'" >'."\n";
    echo '<p><strong>'.$page_section.':</strong></p>';
    echo "<select id='".$value['id']."' class='post_form' name='".$value['id']."' value='true'>\n";
    echo "<option id='all' value=''>All</option>";
    foreach ($value['options'] as $option_value => $option_list) {
      $checked = ' ';
      echo 'value_id=' . $value['id'] .' value_id=' . get_option($value['id']) . ' options_value=' . $option_value;
      if (get_option($value['id']) == $option_value) {
        $checked = ' checked="checked" ';
      } else if (get_option($value['id']) === FALSE && $value['std'] == $option_value){
        $checked = ' checked="checked" ';
      } else {
        $checked = '';
      }
      echo '<option value="'.$option_list['name'].'" class="level-0" '.$checked.' number="'.($option_list['number']).'" />'.$option_list['name']."</option>\n";
      //$all_categoris .= $option_list['name'] . ',';
    }
    echo "</select>\n </div>";
    //echo '<script>jQuery("#all").val("'.$all_categoris.'")</\script>';
    $this->create_closing_tag($value,$current_layout);
	}

  /**
   * Creates the form.
   *
   * @param $value
   * @return void
   */
  public function create_header_3($value,$current_layout) {
    echo '<div class="divider"><h3 class="header-2">'.$value['name']."</h3>\n";
    if(isset($value['desc'])){
      echo '<p>' . $value['desc']. '</p></div>';
    }
  }

  /**
   * Creates the form.
   *
   * @param $options
   * @return void
   */
  private function create_form($options) {
		?> <form id='options_form' method='post' name='form' >
      <?php
		foreach ($options as $value) {
			switch ( $value['type'] ) {
				case "sub-section-3": {
          $current_layout = $value['layout'];
          $this->create_header_3($value,$current_layout);
          switch ($value['layout']) {
            case "grid":{
               ?> <div class='grid' data-masonry='{ "itemSelector": ".grid-item", "columnWidth": 300 }'> <?php
              break;
            }
            case "list":{
              echo "<ul>";
              break;
            }
            case "table":{
              echo "<table>";
              break;
            }
          }
        }
				case "text": {
          $this->create_section_for_text($value,$current_layout);
          break;
        }
				case "textarea": {
          $this->create_section_for_textarea($value,$current_layout);
          break;
        }
				case "multi-select": {
          $this->create_section_for_multi_select($value,$current_layout);
          break;
        }
				case "radio": {
          $this->create_section_for_radio($value,$current_layout);
          break;
        }
				case "color-picker": {
          $this->create_section_for_color_picker($value,$current_layout);
          break;
        }
				case "select": {
          $this->create_section_for_category_select('first section',$value,$current_layout);
          break;
        }
				case "select-2": {
          $this->create_section_for_category_select('second section',$value,$current_layout);
          break;
        }
			}
		}

    switch($current_layout){
      case "grid" :
        echo "</div>";
        break;
      case "list" :
        echo "</ul>";
        break;
      case "table" :
        echo "</table>";
        break;
    }

		?>
    <br>
    <br>
		<input name="save" type="button" value="Save" class="button button-primary" onclick="submit_form(this, document.forms['form'])" />
		<input name="reset_all" type="button" value="Reset to default values" class="button" onclick="submit_form(this, document.forms['form'])" />
		<input type="hidden" name="formaction" value="default" />

     <script> function submit_form(element, form){
				 form['formaction'].value = element.name;
				 form.submit();
			 } </script>

		</form>
	<?php }

    /**
     * main options page callback
     */
    public function create_menu_page(){
      $this->create_wrap_before();

      $this->create_wrap_after();
    }

    /**
     * main options page callback
     */
    public function create_layout_page(){
      $this->create_wrap_before();
      $this->create_form($this->layout_options);
      $this->create_wrap_after();
    }

    /**
     * main options page callback
     */
    public function create_style_page(){
      $this->create_wrap_before();
      $this->create_form($this->style_options);
      $this->create_pattern_index();
      $this->create_color_index();
      $this->create_wrap_after();
    }

    /**
     * color page callback
     */
    public function create_color_page(){
      $this->create_wrap_before();
      $this->create_form($this->color_options);
      $this->create_color_index();
      $this->create_wrap_after();
    }

    /**
     * images page callback
     */
    public function create_images_page(){
      $this->create_wrap_before();
      $this->create_form($this->image_options);
      $this->create_wrap_after();
    }

    /**
     * Add admin settings pages
     */
    public function add_admin(){
      if ( isset($_GET['page']) && $_GET['page'] == 'mx-layout-settings') {

          if ( isset($_REQUEST['formaction']) && 'save' == $_REQUEST['formaction'] ) {
              foreach ($this->layout_options as $value){
                if(isset($value['id'])){
                  if( isset( $value['id'] ) && isset( $_REQUEST[ $value['id'] ] ) ) {
                      update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
                  } else {
                    if( isset( $value['id'] ) ) {
                    delete_option( $value['id'] );
                   }
                  }
                }
              }
              $layout_options = $this->layout_options;
              header("Location: admin.php?page=mx-layout-settings&saved=true");
              die;
          }
          else if(isset($_REQUEST['formaction']) && 'reset_all' == $_REQUEST['formaction']) {
              foreach ($this->layout_options as $value) {
                if( isset( $value['id'] ) ) {
                delete_option( $value['id'] );
               }
              }
              $layout_options = $this->layout_options;
              header("Location: admin.php?page=mx-layout-settings&".$_REQUEST['formaction']."=true");
              die;
          }
      } elseif(isset($_GET['page']) && $_GET['page'] == 'mx-images-settings') {
        if (isset($_REQUEST['formaction']) &&  'save' == $_REQUEST['formaction'] ) {
            foreach ($this->image_options as $value) {
                if( isset( $value['id'] ) && isset($_REQUEST[ $value['id'] ] ) ) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
                }
                else {
                  if( isset( $value['id'] ) ) {
                  delete_option( $value['id'] );
                 }
                }
            }
            $image_options = $this->image_options;
            header("Location: admin.php?page=mx-images-settings&saved=true");
            die;
        } elseif(isset($_REQUEST['formaction']) && 'reset_all' == $_REQUEST['formaction']) {
            foreach ($this->image_options as $value) {
              if( isset( $value['id'] ) ) {
              delete_option( $value['id'] );
             }
            }
            $image_options = $this->image_options;
            header("Location: admin.php?page=mx-images-settings&".$_REQUEST['formaction']."=true");
            die;
        }
      } elseif(isset($_GET['page']) && $_GET['page'] == 'mx-color-settings') {
        if (isset($_REQUEST['formaction']) &&  'save' == $_REQUEST['formaction'] ) {
            foreach ($this->color_options as $value) {
                if( isset( $value['id'] ) && isset($_REQUEST[ $value['id'] ] )) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
                    if ($value['parent'] == 'mx-color-options'){
                        $this->set_sass_color($value,$_REQUEST[ $value['id']]);
                    }
                }
                else {
                  if( isset( $value['id'] ) ) {
                  delete_option( $value['id'] );
                 }
                }
            }
            $color_options = $this->color_options;
            header("Location: admin.php?page=mx-color-settings&saved=true");
            die;
         }elseif(isset($_REQUEST['formaction']) && 'reset_all' == $_REQUEST['formaction']) {
            foreach ($this->color_options as $value) {
                if( isset( $value['id'] ) ) {
                delete_option( $value['id'] );
               }
            }
            $color_options = $this->color_options;
            header("Location: admin.php?page=mx-color-settings&".$_REQUEST['formaction']."=true");
            die;
        }
      } elseif(isset($_GET['page']) && $_GET['page'] == 'mx-style-settings') {
        if (isset($_REQUEST['formaction']) &&  'save' == $_REQUEST['formaction'] ) {
            foreach ($this->style_options as $value) {
                if( isset( $value['id'] ) && isset($_REQUEST[ $value['id'] ] ) ) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
                }
                else {
                  if( isset( $value['id'] ) ) {
                  delete_option( $value['id'] );
                 }
                }
            }
            $color_options = $this->style_options;
            header("Location: admin.php?page=mx-style-settings&saved=true");
            die;
         } elseif(isset($_REQUEST['formaction']) && 'reset_all' == $_REQUEST['formaction']) {
            foreach ($this->style_options as $value) {
                if( isset( $value['id'] ) ) {
                delete_option( $value['id'] );
               }
            }
            $color_options = $this->style_options;
            header("Location: admin.php?page=mx-style-settings&".$_REQUEST['formaction']."=true");

        }
      }


        // This page will be at the root menu
        add_menu_page(
            'MaterializeX Settings',
            'MaterializeX',
            'manage_options',
            'mx-settings',
            array( $this, 'create_menu_page' ),
            get_template_directory_uri() . '/assets/images/mx-modern-clean-ico.png'
        );

        add_submenu_page(
            'mx-settings',
            'Layout Settings',
            'Layout',
            'manage_options',
            'mx-layout-settings',
            array( $this, 'create_layout_page' )
        );

        add_submenu_page(
            'mx-settings',
            'Style Settings',
            'Styles',
            'manage_options',
            'mx-style-settings',
            array( $this, 'create_style_page' )
        );

        add_submenu_page(
            'mx-settings',
            'Color Settings',
            'Color',
            'manage_options',
            'mx-color-settings',
            array( $this, 'create_color_page' )
        );

        add_submenu_page(
            'mx-settings',
            'Image Settings',
            'Image',
            'manage_options',
            'mx-images-settings',
            array( $this, 'create_images_page' )
        );
    }

    /**
     * Create the color index
     *
     */
    public function create_color_index(){
      ?>
      <h3 class="">Color Index:</h3>
      <div class='grid dynamic-color' data-masonry='{ "itemSelector": ".grid-item", "columnWidth": 300 }'>
                  <div class="grid-item">
                  <div class="red lighten-5">#ffebee red lighten-5</div>
                  <div class="red lighten-4">#ffcdd2 red lighten-4</div>
                  <div class="red lighten-3">#ef9a9a red lighten-3</div>
                  <div class="red lighten-2">#e57373 red lighten-2</div>
                  <div class="red lighten-1">#ef5350 red lighten-1</div>
                  <div class="red">#f44336 red</div>
                  <div class="red darken-1" style="color: rgba(255, 255, 255, 0.9);">#e53935 red darken-1</div>
                  <div class="red darken-2" style="color: rgba(255, 255, 255, 0.9);">#d32f2f red darken-2</div>
                  <div class="red darken-3" style="color: rgba(255, 255, 255, 0.9);">#c62828 red darken-3</div>
                  <div class="red darken-4" style="color: rgba(255, 255, 255, 0.9);">#b71c1c red darken-4</div>
                  <div class="red accent-1">#ff8a80 red accent-1</div>
                  <div class="red accent-2">#ff5252 red accent-2</div>
                  <div class="red accent-3">#ff1744 red accent-3</div>
                  <div class="red accent-4">#d50000 red accent-4</div>
                </div>

                <div class="grid-item">
                  <div class="pink lighten-5">#fce4ec pink lighten-5</div>
                  <div class="pink lighten-4">#f8bbd0 pink lighten-4</div>
                  <div class="pink lighten-3">#f48fb1 pink lighten-3</div>
                  <div class="pink lighten-2">#f06292 pink lighten-2</div>
                  <div class="pink lighten-1">#ec407a pink lighten-1</div>
                  <div class="pink">#e91e63 pink</div>
                  <div class="pink darken-1" style="color: rgba(255, 255, 255, 0.9);">#d81b60 pink darken-1</div>
                  <div class="pink darken-2" style="color: rgba(255, 255, 255, 0.9);">#c2185b pink darken-2</div>
                  <div class="pink darken-3" style="color: rgba(255, 255, 255, 0.9);">#ad1457 pink darken-3</div>
                  <div class="pink darken-4" style="color: rgba(255, 255, 255, 0.9);">#880e4f pink darken-4</div>
                  <div class="pink accent-1">#ff80ab pink accent-1</div>
                  <div class="pink accent-2">#ff4081 pink accent-2</div>
                  <div class="pink accent-3">#f50057 pink accent-3</div>
                  <div class="pink accent-4">#c51162 pink accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="purple lighten-5">#f3e5f5 purple lighten-5</div>
                  <div class="purple lighten-4">#e1bee7 purple lighten-4</div>
                  <div class="purple lighten-3">#ce93d8 purple lighten-3</div>
                  <div class="purple lighten-2">#ba68c8 purple lighten-2</div>
                  <div class="purple lighten-1">#ab47bc purple lighten-1</div>
                  <div class="purple">#9c27b0 purple</div>
                  <div class="purple darken-1" style="color: rgba(255, 255, 255, 0.9);">#8e24aa purple darken-1</div>
                  <div class="purple darken-2" style="color: rgba(255, 255, 255, 0.9);">#7b1fa2 purple darken-2</div>
                  <div class="purple darken-3" style="color: rgba(255, 255, 255, 0.9);">#6a1b9a purple darken-3</div>
                  <div class="purple darken-4" style="color: rgba(255, 255, 255, 0.9);">#4a148c purple darken-4</div>
                  <div class="purple accent-1">#ea80fc purple accent-1</div>
                  <div class="purple accent-2">#e040fb purple accent-2</div>
                  <div class="purple accent-3">#d500f9 purple accent-3</div>
                  <div class="purple accent-4">#aa00ff purple accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="deep-purple lighten-5">#ede7f6 deep-purple lighten-5</div>
                  <div class="deep-purple lighten-4">#d1c4e9 deep-purple lighten-4</div>
                  <div class="deep-purple lighten-3">#b39ddb deep-purple lighten-3</div>
                  <div class="deep-purple lighten-2">#9575cd deep-purple lighten-2</div>
                  <div class="deep-purple lighten-1">#7e57c2 deep-purple lighten-1</div>
                  <div class="deep-purple">#673ab7 deep-purple</div>
                  <div class="deep-purple darken-1" style="color: rgba(255, 255, 255, 0.9);">#5e35b1 deep-purple darken-1</div>
                  <div class="deep-purple darken-2" style="color: rgba(255, 255, 255, 0.9);">#512da8 deep-purple darken-2</div>
                  <div class="deep-purple darken-3" style="color: rgba(255, 255, 255, 0.9);">#4527a0 deep-purple darken-3</div>
                  <div class="deep-purple darken-4" style="color: rgba(255, 255, 255, 0.9);">#311b92 deep-purple darken-4</div>
                  <div class="deep-purple accent-1">#b388ff deep-purple accent-1</div>
                  <div class="deep-purple accent-2">#7c4dff deep-purple accent-2</div>
                  <div class="deep-purple accent-3">#651fff deep-purple accent-3</div>
                  <div class="deep-purple accent-4">#6200ea deep-purple accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="indigo lighten-5">#e8eaf6 indigo lighten-5</div>
                  <div class="indigo lighten-4">#c5cae9 indigo lighten-4</div>
                  <div class="indigo lighten-3">#9fa8da indigo lighten-3</div>
                  <div class="indigo lighten-2">#7986cb indigo lighten-2</div>
                  <div class="indigo lighten-1">#5c6bc0 indigo lighten-1</div>
                  <div class="indigo">#3f51b5 indigo</div>
                  <div class="indigo darken-1" style="color: rgba(255, 255, 255, 0.9);">#3949ab indigo darken-1</div>
                  <div class="indigo darken-2" style="color: rgba(255, 255, 255, 0.9);">#303f9f indigo darken-2</div>
                  <div class="indigo darken-3" style="color: rgba(255, 255, 255, 0.9);">#283593 indigo darken-3</div>
                  <div class="indigo darken-4" style="color: rgba(255, 255, 255, 0.9);">#1a237e indigo darken-4</div>
                  <div class="indigo accent-1">#8c9eff indigo accent-1</div>
                  <div class="indigo accent-2">#536dfe indigo accent-2</div>
                  <div class="indigo accent-3">#3d5afe indigo accent-3</div>
                  <div class="indigo accent-4">#304ffe indigo accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="blue lighten-5">#e3f2fd blue lighten-5</div>
                  <div class="blue lighten-4">#bbdefb blue lighten-4</div>
                  <div class="blue lighten-3">#90caf9 blue lighten-3</div>
                  <div class="blue lighten-2">#64b5f6 blue lighten-2</div>
                  <div class="blue lighten-1">#42a5f5 blue lighten-1</div>
                  <div class="blue">#2196f3 blue</div>
                  <div class="blue darken-1" style="color: rgba(255, 255, 255, 0.9);">#1e88e5 blue darken-1</div>
                  <div class="blue darken-2" style="color: rgba(255, 255, 255, 0.9);">#1976d2 blue darken-2</div>
                  <div class="blue darken-3" style="color: rgba(255, 255, 255, 0.9);">#1565c0 blue darken-3</div>
                  <div class="blue darken-4" style="color: rgba(255, 255, 255, 0.9);">#0d47a1 blue darken-4</div>
                  <div class="blue accent-1">#82b1ff blue accent-1</div>
                  <div class="blue accent-2">#448aff blue accent-2</div>
                  <div class="blue accent-3">#2979ff blue accent-3</div>
                  <div class="blue accent-4">#2962ff blue accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="light-blue lighten-5">#e1f5fe light-blue lighten-5</div>
                  <div class="light-blue lighten-4">#b3e5fc light-blue lighten-4</div>
                  <div class="light-blue lighten-3">#81d4fa light-blue lighten-3</div>
                  <div class="light-blue lighten-2">#4fc3f7 light-blue lighten-2</div>
                  <div class="light-blue lighten-1">#29b6f6 light-blue lighten-1</div>
                  <div class="light-blue">#03a9f4 light-blue</div>
                  <div class="light-blue darken-1" style="color: rgba(255, 255, 255, 0.9);">#039be5 light-blue darken-1</div>
                  <div class="light-blue darken-2" style="color: rgba(255, 255, 255, 0.9);">#0288d1 light-blue darken-2</div>
                  <div class="light-blue darken-3" style="color: rgba(255, 255, 255, 0.9);">#0277bd light-blue darken-3</div>
                  <div class="light-blue darken-4" style="color: rgba(255, 255, 255, 0.9);">#01579b light-blue darken-4</div>
                  <div class="light-blue accent-1">#80d8ff light-blue accent-1</div>
                  <div class="light-blue accent-2">#40c4ff light-blue accent-2</div>
                  <div class="light-blue accent-3">#00b0ff light-blue accent-3</div>
                  <div class="light-blue accent-4">#0091ea light-blue accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="cyan lighten-5">#e0f7fa cyan lighten-5</div>
                  <div class="cyan lighten-4">#b2ebf2 cyan lighten-4</div>
                  <div class="cyan lighten-3">#80deea cyan lighten-3</div>
                  <div class="cyan lighten-2">#4dd0e1 cyan lighten-2</div>
                  <div class="cyan lighten-1">#26c6da cyan lighten-1</div>
                  <div class="cyan">#00bcd4 cyan</div>
                  <div class="cyan darken-1" style="color: rgba(255, 255, 255, 0.9);">#00acc1 cyan darken-1</div>
                  <div class="cyan darken-2" style="color: rgba(255, 255, 255, 0.9);">#0097a7 cyan darken-2</div>
                  <div class="cyan darken-3" style="color: rgba(255, 255, 255, 0.9);">#00838f cyan darken-3</div>
                  <div class="cyan darken-4" style="color: rgba(255, 255, 255, 0.9);">#006064 cyan darken-4</div>
                  <div class="cyan accent-1">#84ffff cyan accent-1</div>
                  <div class="cyan accent-2">#18ffff cyan accent-2</div>
                  <div class="cyan accent-3">#00e5ff cyan accent-3</div>
                  <div class="cyan accent-4">#00b8d4 cyan accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="teal lighten-5">#e0f2f1 teal lighten-5</div>
                  <div class="teal lighten-4">#b2dfdb teal lighten-4</div>
                  <div class="teal lighten-3">#80cbc4 teal lighten-3</div>
                  <div class="teal lighten-2">#4db6ac teal lighten-2</div>
                  <div class="teal lighten-1">#26a69a teal lighten-1</div>
                  <div class="teal">#009688 teal</div>
                  <div class="teal darken-1" style="color: rgba(255, 255, 255, 0.9);">#00897b teal darken-1</div>
                  <div class="teal darken-2" style="color: rgba(255, 255, 255, 0.9);">#00796b teal darken-2</div>
                  <div class="teal darken-3" style="color: rgba(255, 255, 255, 0.9);">#00695c teal darken-3</div>
                  <div class="teal darken-4" style="color: rgba(255, 255, 255, 0.9);">#004d40 teal darken-4</div>
                  <div class="teal accent-1">#a7ffeb teal accent-1</div>
                  <div class="teal accent-2">#64ffda teal accent-2</div>
                  <div class="teal accent-3">#1de9b6 teal accent-3</div>
                  <div class="teal accent-4">#00bfa5 teal accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="green lighten-5">#e8f5e9 green lighten-5</div>
                  <div class="green lighten-4">#c8e6c9 green lighten-4</div>
                  <div class="green lighten-3">#a5d6a7 green lighten-3</div>
                  <div class="green lighten-2">#81c784 green lighten-2</div>
                  <div class="green lighten-1">#66bb6a green lighten-1</div>
                  <div class="green">#4caf50 green</div>
                  <div class="green darken-1" style="color: rgba(255, 255, 255, 0.9);">#43a047 green darken-1</div>
                  <div class="green darken-2" style="color: rgba(255, 255, 255, 0.9);">#388e3c green darken-2</div>
                  <div class="green darken-3" style="color: rgba(255, 255, 255, 0.9);">#2e7d32 green darken-3</div>
                  <div class="green darken-4" style="color: rgba(255, 255, 255, 0.9);">#1b5e20 green darken-4</div>
                  <div class="green accent-1">#b9f6ca green accent-1</div>
                  <div class="green accent-2">#69f0ae green accent-2</div>
                  <div class="green accent-3">#00e676 green accent-3</div>
                  <div class="green accent-4">#00c853 green accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="light-green lighten-5">#f1f8e9 light-green lighten-5</div>
                  <div class="light-green lighten-4">#dcedc8 light-green lighten-4</div>
                  <div class="light-green lighten-3">#c5e1a5 light-green lighten-3</div>
                  <div class="light-green lighten-2">#aed581 light-green lighten-2</div>
                  <div class="light-green lighten-1">#9ccc65 light-green lighten-1</div>
                  <div class="light-green">#8bc34a light-green</div>
                  <div class="light-green darken-1" style="color: rgba(255, 255, 255, 0.9);">#7cb342 light-green darken-1</div>
                  <div class="light-green darken-2" style="color: rgba(255, 255, 255, 0.9);">#689f38 light-green darken-2</div>
                  <div class="light-green darken-3" style="color: rgba(255, 255, 255, 0.9);">#558b2f light-green darken-3</div>
                  <div class="light-green darken-4" style="color: rgba(255, 255, 255, 0.9);">#33691e light-green darken-4</div>
                  <div class="light-green accent-1">#ccff90 light-green accent-1</div>
                  <div class="light-green accent-2">#b2ff59 light-green accent-2</div>
                  <div class="light-green accent-3">#76ff03 light-green accent-3</div>
                  <div class="light-green accent-4">#64dd17 light-green accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="lime lighten-5">#f9fbe7 lime lighten-5</div>
                  <div class="lime lighten-4">#f0f4c3 lime lighten-4</div>
                  <div class="lime lighten-3">#e6ee9c lime lighten-3</div>
                  <div class="lime lighten-2">#dce775 lime lighten-2</div>
                  <div class="lime lighten-1">#d4e157 lime lighten-1</div>
                  <div class="lime">#cddc39 lime</div>
                  <div class="lime darken-1" style="color: rgba(255, 255, 255, 0.9);">#c0ca33 lime darken-1</div>
                  <div class="lime darken-2" style="color: rgba(255, 255, 255, 0.9);">#afb42b lime darken-2</div>
                  <div class="lime darken-3" style="color: rgba(255, 255, 255, 0.9);">#9e9d24 lime darken-3</div>
                  <div class="lime darken-4" style="color: rgba(255, 255, 255, 0.9);">#827717 lime darken-4</div>
                  <div class="lime accent-1">#f4ff81 lime accent-1</div>
                  <div class="lime accent-2">#eeff41 lime accent-2</div>
                  <div class="lime accent-3">#c6ff00 lime accent-3</div>
                  <div class="lime accent-4">#aeea00 lime accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="yellow lighten-5">#fffde7 yellow lighten-5</div>
                  <div class="yellow lighten-4">#fff9c4 yellow lighten-4</div>
                  <div class="yellow lighten-3">#fff59d yellow lighten-3</div>
                  <div class="yellow lighten-2">#fff176 yellow lighten-2</div>
                  <div class="yellow lighten-1">#ffee58 yellow lighten-1</div>
                  <div class="yellow">#ffeb3b yellow</div>
                  <div class="yellow darken-1" style="color: rgba(255, 255, 255, 0.9);">#fdd835 yellow darken-1</div>
                  <div class="yellow darken-2" style="color: rgba(255, 255, 255, 0.9);">#fbc02d yellow darken-2</div>
                  <div class="yellow darken-3" style="color: rgba(255, 255, 255, 0.9);">#f9a825 yellow darken-3</div>
                  <div class="yellow darken-4" style="color: rgba(255, 255, 255, 0.9);">#f57f17 yellow darken-4</div>
                  <div class="yellow accent-1">#ffff8d yellow accent-1</div>
                  <div class="yellow accent-2">#ffff00 yellow accent-2</div>
                  <div class="yellow accent-3">#ffea00 yellow accent-3</div>
                  <div class="yellow accent-4">#ffd600 yellow accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="amber lighten-5">#fff8e1 amber lighten-5</div>
                  <div class="amber lighten-4">#ffecb3 amber lighten-4</div>
                  <div class="amber lighten-3">#ffe082 amber lighten-3</div>
                  <div class="amber lighten-2">#ffd54f amber lighten-2</div>
                  <div class="amber lighten-1">#ffca28 amber lighten-1</div>
                  <div class="amber">#ffc107 amber</div>
                  <div class="amber darken-1" style="color: rgba(255, 255, 255, 0.9);">#ffb300 amber darken-1</div>
                  <div class="amber darken-2" style="color: rgba(255, 255, 255, 0.9);">#ffa000 amber darken-2</div>
                  <div class="amber darken-3" style="color: rgba(255, 255, 255, 0.9);">#ff8f00 amber darken-3</div>
                  <div class="amber darken-4" style="color: rgba(255, 255, 255, 0.9);">#ff6f00 amber darken-4</div>
                  <div class="amber accent-1">#ffe57f amber accent-1</div>
                  <div class="amber accent-2">#ffd740 amber accent-2</div>
                  <div class="amber accent-3">#ffc400 amber accent-3</div>
                  <div class="amber accent-4">#ffab00 amber accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="orange lighten-5">#fff3e0 orange lighten-5</div>
                  <div class="orange lighten-4">#ffe0b2 orange lighten-4</div>
                  <div class="orange lighten-3">#ffcc80 orange lighten-3</div>
                  <div class="orange lighten-2">#ffb74d orange lighten-2</div>
                  <div class="orange lighten-1">#ffa726 orange lighten-1</div>
                  <div class="orange">#ff9800 orange</div>
                  <div class="orange darken-1" style="color: rgba(255, 255, 255, 0.9);">#fb8c00 orange darken-1</div>
                  <div class="orange darken-2" style="color: rgba(255, 255, 255, 0.9);">#f57c00 orange darken-2</div>
                  <div class="orange darken-3" style="color: rgba(255, 255, 255, 0.9);">#ef6c00 orange darken-3</div>
                  <div class="orange darken-4" style="color: rgba(255, 255, 255, 0.9);">#e65100 orange darken-4</div>
                  <div class="orange accent-1">#ffd180 orange accent-1</div>
                  <div class="orange accent-2">#ffab40 orange accent-2</div>
                  <div class="orange accent-3">#ff9100 orange accent-3</div>
                  <div class="orange accent-4">#ff6d00 orange accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="deep-orange lighten-5">#fbe9e7 deep-orange lighten-5</div>
                  <div class="deep-orange lighten-4">#ffccbc deep-orange lighten-4</div>
                  <div class="deep-orange lighten-3">#ffab91 deep-orange lighten-3</div>
                  <div class="deep-orange lighten-2">#ff8a65 deep-orange lighten-2</div>
                  <div class="deep-orange lighten-1">#ff7043 deep-orange lighten-1</div>
                  <div class="deep-orange">#ff5722 deep-orange</div>
                  <div class="deep-orange darken-1" style="color: rgba(255, 255, 255, 0.9);">#f4511e deep-orange darken-1</div>
                  <div class="deep-orange darken-2" style="color: rgba(255, 255, 255, 0.9);">#e64a19 deep-orange darken-2</div>
                  <div class="deep-orange darken-3" style="color: rgba(255, 255, 255, 0.9);">#d84315 deep-orange darken-3</div>
                  <div class="deep-orange darken-4" style="color: rgba(255, 255, 255, 0.9);">#bf360c deep-orange darken-4</div>
                  <div class="deep-orange accent-1">#ff9e80 deep-orange accent-1</div>
                  <div class="deep-orange accent-2">#ff6e40 deep-orange accent-2</div>
                  <div class="deep-orange accent-3">#ff3d00 deep-orange accent-3</div>
                  <div class="deep-orange accent-4">#dd2c00 deep-orange accent-4</div>
                </div>
                <div class="grid-item">
                  <div class="brown lighten-5">#efebe9 brown lighten-5</div>
                  <div class="brown lighten-4">#d7ccc8 brown lighten-4</div>
                  <div class="brown lighten-3">#bcaaa4 brown lighten-3</div>
                  <div class="brown lighten-2">#a1887f brown lighten-2</div>
                  <div class="brown lighten-1">#8d6e63 brown lighten-1</div>
                  <div class="brown">#795548 brown</div>
                  <div class="brown darken-1" style="color: rgba(255, 255, 255, 0.9);">#6d4c41 brown darken-1</div>
                  <div class="brown darken-2" style="color: rgba(255, 255, 255, 0.9);">#5d4037 brown darken-2</div>
                  <div class="brown darken-3" style="color: rgba(255, 255, 255, 0.9);">#4e342e brown darken-3</div>
                  <div class="brown darken-4" style="color: rgba(255, 255, 255, 0.9);">#3e2723 brown darken-4</div>
                </div>
                <div class="grid-item">
                  <div class="grey lighten-5">#fafafa grey lighten-5</div>
                  <div class="grey lighten-4">#f5f5f5 grey lighten-4</div>
                  <div class="grey lighten-3">#eeeeee grey lighten-3</div>
                  <div class="grey lighten-2">#e0e0e0 grey lighten-2</div>
                  <div class="grey lighten-1">#bdbdbd grey lighten-1</div>
                  <div class="grey">#9e9e9e grey</div>
                  <div class="grey darken-1" style="color: rgba(255, 255, 255, 0.9);">#757575 grey darken-1</div>
                  <div class="grey darken-2" style="color: rgba(255, 255, 255, 0.9);">#616161 grey darken-2</div>
                  <div class="grey darken-3" style="color: rgba(255, 255, 255, 0.9);">#424242 grey darken-3</div>
                  <div class="grey darken-4" style="color: rgba(255, 255, 255, 0.9);">#212121 grey darken-4</div>
                </div>
              </div>
      <?php
    }

    /**
     * Create the pattern index
     *
     */
    public function create_pattern_index(){
      ?>
      <div class="row">
        <h3 class="">Pattern Index:</h3>
        <div class='grid' data-masonry='{ "itemSelector": ".grid-item", "columnWidth": 400 }'>
          <!-- start of grid -->
          <div class="grid-item grid-item--width2">
              <div class="card pattern-demo diamond-pattern white-text"><div class="card-content">
                <h3 class="card-title white-text">Diamond pattern</h3>
                <p>
                  diamond-pattern
                </p>
              </div>
            </div>
          </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo gears-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Gears pattern</h3>
            <p>
              gears-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo hexagon-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Haxagon pattern</h3>
            <p>
              hexagon-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo topography-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Topography pattern</h3>
            <p>
              topography-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo dominoes-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Dominoes pattern</h3>
            <p>
              dominoes-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo autumn-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Autumn pattern</h3>
            <p>
              authmn-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class=" card pattern-demo pcb-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">PCB pattern</h3>
            <p>
              pcb-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo skull-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Skull pattern</h3>
            <p>
              skull-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo squares-in-squares-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Squares in squares pattern</h3>
            <p>

            </p>
              squares-in-squares-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo texture-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Texture pattern</h3>
            <p>

            </p>
              texture-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo formal-invitation-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Formal invitation pattern</h3>
            <p>

            </p>
              formal-invitation-patter
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo graph-paper-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Graph paper pattern</h3>
            <p>

            </p>
              graph-paper-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class=" card pattern-demo falling-triangles-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Falling triangles pattern</h3>
            <p>

            </p>
              falling-triangles-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo piano-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Piano pattern</h3>
            <p>

            </p>
              piano-pattern
            </p>
          </div>
        </div>
        </div>

        <div class="grid-item grid-item--width2">
          <div class="card pattern-demo charlie-brown-pattern white-text"><div class="card-content">
            <h3 class="card-title white-text">Charlie brown pattern</h3>
            <p>

            </p>
              charlie-brown-pattern
            </p>
          </div>
        </div>
        </div>
        <!-- end of grid -->
      </div>
    </div>

      <?php
    }

    /**
     * Set Sass Variables
     *
     * @param array $option contains the option object for modifying the SASS
     * @param string $value contains the value to be applied
     */
    public function set_sass_color($option = array(),$value){
      if(isset($option['name']) && isset($value)){
        $values = str_split ( $value , 1 );
        if($value[1] == $values[2] && $value[3] == $values[4] && $value[5] == $values[6]){
          $value = $values[0].$values[1].$values[3].$values[5];
        }
        $option_name = str_replace(' ','-',strtolower($option['name']));
        $search = '/('.$option_name.')[:](.*?)[;]/';
        $replace = $option_name.': '.$value.';';
        $variables = file_get_contents(get_template_directory().'/assets/styles/common/_variables.scss');
        $variables = preg_replace($search,$replace,$variables);
        file_put_contents(get_template_directory().'/assets/styles/common/_variables.scss', $variables);
      }
    }

    /**
     *
     *
     *
     */
    private function compile_sass(){

    }

}
if( is_admin() )
  $matx_options = new MatXOptions();
