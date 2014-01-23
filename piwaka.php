<?php
/*
Plugin Name: Piwaka
Plugin URI: http://piwaka.tanyagray.co.nz/
Description: Provides a url and custom post type for posting pictures from Twitter apps directly to your blog.
Version: 1.0
Author: Tanya Gray
Author URI: http://tanyagray.co.nz
License: GPL2
*/

include_once( WP_PLUGIN_DIR . '/piwaka/functions.php' );

include_once( WP_PLUGIN_DIR . '/piwaka/admin/PiwakaAdmin.php' );
include_once( WP_PLUGIN_DIR . '/piwaka/shortcodes/FeaturedImage.php' );
include_once( WP_PLUGIN_DIR . '/piwaka/shortcodes/ImageGallery.php' );


class Piwaka {

	public  $plugin_url,
			$plugin_dir,
			$plugin_name,
			$plugin_basename;

	/*
	* Construct
	*/
	function __construct() {

		// The Plugin URL Path
	    $this->plugin_url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
	    // The Plugin DIR Path
	    $this->plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
	    // The Plugin Name. Derived from the plugin folder name.
	    $this->plugin_name = basename(dirname(__FILE__));
	    // The Plugin Basename
	    $this->plugin_basename = plugin_basename(__FILE__);

	    // activation
	    register_activation_hook( __FILE__, array( $this, 'activate_piwaka' ) );
	    // deactivation
	    register_deactivation_hook( __FILE__, array( $this, 'deactivate_piwaka' ) );

    	// Register the custom post type
    	add_action( 'init', array( $this, 'register_post_type' ) );
    	// Add URL rewrite for the api location
    	add_action( 'generate_rewrite_rules', array( $this, 'redirect_api_requests' ) );
    	// Display the icon for the custom post type
    	add_action( 'admin_head', array( $this, 'cpt_icons') );
    	

	}


	function activate_piwaka() {

		$this->redirect_api_requests();

		// Ensure the $wp_rewrite global is loaded
		//global $wp_rewrite;

		// Update the rules in htaccess
		//$wp_rewrite->flush_rules(true);

		flush_rewrite_rules();

	}

	function deactivate_piwaka() {

		// Ensure the $wp_rewrite global is loaded
		//global $wp_rewrite;

		// TODO: Remove the rules we added

		// Update the rules in htaccess
		//$wp_rewrite->flush_rules(true);

		flush_rewrite_rules();

	}


	/*
	* Register the 'trips' post type as a menu item
	*/
	function register_post_type() {

		$args = array(
			'labels'	=>	array(
	            'all_items' => 'All Photos',
				'menu_name'	          =>	'Photos',
				'singular_name'       =>	'Post',
			 	'edit_item'           =>	'Edit Post',
			 	'new_item'            =>	'New Post',
			 	'view_item'           =>	'View Post',
			 	'items_archive'       =>	'Post Archive',
			 	'search_items'        =>	'Search Posts',
			 	'not_found'	          =>	'No posts found',
			 	'not_found_in_trash'  =>	'No posts found in trash'	
			),
			'supports'		=>	array( 'title', 'editor', 'thumbnail' ),	
			'show_in_nav_menus'  =>  true,			
			'show_in_menu'  =>  true,
			'menu_position'	=>	5,
			'public'		=>	true,
			'rewrite' 		=>  array('slug' => 'photo')
		);


		register_post_type( 'piwaka', $args );
	}


	/*
	 * Allow us to use a short URL to access the piwaka API
	 * eg. http://mysite.com/piwaka/ redirects to
	 * http://mysite.com/wp-content/plugins/piwaka/
	 */
	function redirect_api_requests() {
    	
		add_rewrite_rule(  
        	'piwaka/upload/?$', 
        	'wp-content/plugins/piwaka/api/upload.php', 
        	'top' 
        ); 

	}


	function cpt_icons() {
	    
	    echo '<style type="text/css" media="screen">'.
	        	'#menu-posts-piwaka .wp-menu-image {'.
	            	'background: url('.$this->plugin_url.'/images/piwaka-post-icon.png) no-repeat 6px -17px !important;'.
	        	'}'.
	        	'#menu-posts-piwaka:hover .wp-menu-image, #menu-posts-piwaka.wp-has-current-submenu .wp-menu-image {'.
	            	'background-position:6px 7px !important;'.
	        	'}'.
	    	'</style>';
	}


}

$piwaka = new Piwaka();


?>