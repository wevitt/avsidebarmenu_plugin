<?php 


    /*
    Plugin Name: AV Sidebar Menu
    Plugin URI: http://www.dinamiqa.com/
    Description: Crea un metabox personalizzabile per ogni pagina in cui definire il menu e fornisce un widget per la renderizzazione.
    Author: Andrea R., Vittorio I.
    Version: 1.0
    Author URI: http://www.dinamiqa.com/
    */


    // PLUGIN MAIN CLASS
    class sidebarMenu {

        /**
         * Construct the plugin object
         */

        public function __construct() {
		
			// add_action( 'load-post-new.php', array(__CLASS__, "check_new_post_request") );
			// add_action( 'load-post.php', array(__CLASS__, "check_edit_post_request") );
			// add_action( 'save_post', array(__CLASS__, "add_plan_data_to_post"),10,2);
			add_action( 'widgets_init', array(__CLASS__, 'register_sidebar_menu_widget' ) );
			add_action("add_meta_boxes", array(__CLASS__, "add_sidebar_menu_metabox") );
			add_action("save_post", array(__CLASS__, "save_sidebar_menu_metabox"), 10, 3 );
		}

		public function register_sidebar_menu_widget() {
	    	register_widget( 'sidebarMenuWidget' );
		}
    
		public function add_sidebar_menu_metabox() {
			add_meta_box("sidebar-menu-meta-box", 'Seleziona Menu', array(__CLASS__, "sidebar_menu_metabox_markup"), "page", "side", "default", null);
		}

		public function save_sidebar_menu_metabox($post_id, $post, $update) {



			 if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
	        return $post_id;

		    if(!current_user_can("edit_post", $post_id))
		        return $post_id;

		    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
		        return $post_id;

		

		    $meta_box_dropdown_value = "";


		    if(isset($_POST["custom_menu"]))
		    {
		        $meta_box_dropdown_value = $_POST["custom_menu"];
		    }   
		    update_post_meta($post_id, "custom_menu", $meta_box_dropdown_value);

		}

		public function sidebar_menu_metabox_markup() {
			global $post;

			 wp_nonce_field(basename(__FILE__), "meta-box-nonce");

	    //print_r(get_post_meta($object->ID, "custom_menu", true));
	    // print_r(get_registered_nav_menus());
			 echo get_post_meta($object->ID, "custom_menu", true);
	    ?>
	        <div>
	            <!-- <label for="meta-box-dropdown">Seleziona Menu </label> -->

<?php 


	
                		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
						$menuArray = array('-- Nessun Menu  --');

						foreach ( $menus as $menu ) {
							$menuArray[] = $menu->name;
						}




	                    $option_values = $menuArray;

?>

	            <select name="custom_menu">
	                <?php 

	                    foreach($option_values as $key => $value) 
	                    {
	                        if($value == get_post_meta($post->ID, "custom_menu", true))
	                        {
	                            ?>
	                                <option selected><?php echo $value; ?></option>
	                            <?php    
	                        }
	                        else
	                        {
	                            ?>
	                                <option><?php echo $value; ?></option>
	                            <?php
	                        }
	                    }
	                ?>
	            </select>

	           
	            
	        </div>
	    <?php  
		}

        /**
         * Activate the plugin
         */

        public static function activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */    

        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
    } // END class main



    // CLASSE MENU WIDGET
    class sidebarMenuWidget extends WP_Widget {
	    function __construct() {
			// Instantiate the parent object
			parent::__construct( 
				// false, 
				// 'Dynamic Menu','Il widget mostra dinamicamente il menu selezionato nel rispettivo metabox della pagina.', 
				// array( 'description' => __( 'Il widget mostra dinamicamente il menu selezionato nel rispettivo metabox della pagina.', 'text_domain' ), ) 


				'sidebar-menu', // Base ID
				__( 'Sidebar Menu', 'sidebar_menu' ), // Name
				array( 'description' => __( 'Il widget mostra dinamicamente il menu selezionato nel rispettivo metabox della pagina.', 'sidebar_menu' ), ) // Args

			);
		}

		function widget( $args, $instance ) {
			global $post; 

			if( get_post_meta($post->ID, 'custom_menu', true) == true ) {

				$sidebarMenu = get_post_meta($post->ID, 'custom_menu', true);

			}

			$bottom_menu_options = array(
				'theme_location'  => '',
				'container'       => 'div',
				'container_id'    => 'sidebar-menu-widget-'.$sidebarMenu,
				'container_class' => 'sidebar-menu-widget',
				'menu' => $sidebarMenu
			);

			echo $sidebarMenu;


			if('-- Nessun Menu --' == $sidebarMenu) return;


			?>

			<div class="widget">
				<h3 class="widget-title">Menu</h3>
				

					<?php
					wp_nav_menu($bottom_menu_options);

					?>
				
			</div>
			
			<?php
		}

		function update( $new_instance, $old_instance ) {
			// Save widget options
		}

		function form( $instance ) {
			?>
			<p>Il widget mostra dinamicamente il menu selezionato nel rispettivo metabox della pagina.</p>
			<?php
		}
	} // END class widget




	function makeMyMenus() {
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		global $menuArray;
		$menuArray = array('-- Nessun Menu  --');

		foreach ( $menus as $menu ) {
			$menuArray[] = $menu->name;
		}

		return $menuArray;		
	}



	// ISTANZIA CLASSE PLUGIN
  	new sidebarMenu;





?>