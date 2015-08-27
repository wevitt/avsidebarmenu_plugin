<?php 


    /*
    Plugin Name: AV Sidebar Menu
    Plugin URI: https://github.com/wevitt/avsidebarmenu_plugin
    Description: Consente di aggiungere un menu personalizzato, ad ogni pagina o post da visualizzare come widget all'interno di una sidebar.
    Author: Andrea Rosati, Vittorio Iovinella
    Version: 1.0
    Author URI: mailbox.rosati@gmail.com, vittorio.iovinella@gmail.com
    Licence: GPLv2
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
    */


    /**
     * PLUGIN MAIN CLASS
    */    
    class sidebarMenu {

        /**
         * Construct the plugin object
         */

        public function __construct() {
			add_action( 'widgets_init', array(__CLASS__, 'register_sidebar_menu_widget' ) );
			add_action("add_meta_boxes", array(__CLASS__, "add_sidebar_menu_metabox") );
			add_action("save_post", array(__CLASS__, "save_sidebar_menu_metabox"), 10, 3 );
			register_deactivation_hook( __FILE__, 'sidebarMenu::deactivate' );
		}

		public function register_sidebar_menu_widget() {
	    	register_widget( 'sidebarMenuWidget' );
		}
    
		public function add_sidebar_menu_metabox() {
			add_meta_box("sidebar-menu-meta-box", 'Seleziona Menu', array(__CLASS__, "sidebar_menu_metabox_markup"), "page", "side", "default", null);
			add_meta_box("sidebar-menu-meta-box", 'Seleziona Menu', array(__CLASS__, "sidebar_menu_metabox_markup"), "post", "side", "default", null);
			}

		public function save_sidebar_menu_metabox($post_id, $post, $update) {
			if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
	        return $post_id;

		    if(!current_user_can("edit_post", $post_id))
		        return $post_id;

		    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
		        return $post_id;

		    $meta_box_dropdown_value = "";


		    if(isset($_POST["sidebar-menu-name"]))
		    {
		        $meta_box_dropdown_value = $_POST["sidebar-menu-name"];
		    }   
		    update_post_meta($post_id, "sidebar-menu-name", $meta_box_dropdown_value);
		}

		public function sidebar_menu_metabox_markup() {
			global $post;

			wp_nonce_field(basename(__FILE__), "meta-box-nonce");
			echo get_post_meta($object->ID, "sidebar-menu-name", true);

		    ?>

	        <div>

				<?php 
			
		    		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
					$menuArray = array('-- No Menu --');

					foreach ( $menus as $menu ) {
						$menuArray[] = $menu->name;
					}

		            $option_values = $menuArray;

				?>

	            <select name="sidebar-menu-name">
	                <?php 
	                    foreach($option_values as $key => $value) 
	                    {
	                        if($value == get_post_meta($post->ID, "sidebar-menu-name", true))
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

        public static function activate() {
            
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */    

        public static function deactivate() {

        	global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => 'sidebar-menu-name' ) );

        } // END public static function deactivate
    } // END class main



    /**
     * CLASS MENU WIDGET
    */    
    class sidebarMenuWidget extends WP_Widget {
	    function __construct() {
			// Instantiate the parent object
			parent::__construct( 
				'av_sidebar-menu', // Base ID
				__( 'AV Sidebar Menu', 'sidebar_menu' ), // Name
				array( 'description' => __( 'Mostra il menu selezionato nell\'appostito box per la pagina o il post.', 'sidebar_menu' ), ) // Args
			);
		}

		function widget( $args, $instance ) {
			global $post; 

			if( get_post_meta($post->ID, 'sidebar-menu-name', true) == true ) {

				$sidebarMenu = get_post_meta($post->ID, 'sidebar-menu-name', true);

			}

			$bottom_menu_options = array(
				'theme_location'  => '',
				'container'       => 'div',
				'container_id'    => 'sidebar-menu-widget-'.$sidebarMenu,
				'container_class' => 'sidebar-menu-widget',
				'menu' => $sidebarMenu
			);

			if('-- No Menu --' == $sidebarMenu) return;

			?>

			<div class="widget">
				<h3 class="widget-title"><?php echo $instance['sidebar-menu-widget-title']; ?></h3>
				

					<?php
					wp_nav_menu($bottom_menu_options);

					?>
			</div>
			
			<?php
		}

		function update( $new_instance, $old_instance ) {
        return $new_instance;
    	}
    	
    	function form( $instance ) {
	        $title = esc_attr($instance['sidebar-menu-widget-title']); ?>
	        <p><label for="<?php echo $this->get_field_id('sidebar-menu-widget-title');?>">
	        Titolo: <input class="widefat" id="<?php echo $this->get_field_id('sidebar-menu-widget-title');?>" name="<?php echo $this->get_field_name('sidebar-menu-widget-title');?>" type="text" value="<?php echo $title; ?>" />
	        </label></p>
	        <p>Mostra il menu selezionato nell'appostito box per la pagina o il post.</p>
        <?php
    }
	} // END class widget





	/**
      * INIT PLUGIN CLASS
	*/    
  	new sidebarMenu;





?>
